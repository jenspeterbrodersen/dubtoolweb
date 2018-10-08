import 'jquery';
import 'lodash';
import './style.css';


function component(){
  let element = document.createElement('div');
  element.innerHTML =  
    `<div id="wrapper" class="toggled">        
    <div id="page-content-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Convert manuscript from XML to XLSX</h1>
                    <div class="container filebutton">
                        <label class="btn btn-default btn-file">Upload XML file...<input type="file" id="inputfile" style="display: none;"></label>
                        <label class="btn btn-default btn-file" id="download" style="display:none"><a href="file.csv" id="link">Download XLSX file</a></label>
                    </div>
                    <div class="container filename"><div id="list"></div></div>
                    <div class="container strip col-md-12"><div id="csv"></div></div>                    
                </div>
            </div>
        </div>
    </div>
  </div>`

return element;

}

document.body.appendChild(component());

console.log("09");
window.onload = function() {

  // Adjust namePos and dialoguePos to match name position + dialogue position in your xml file
  var namePos = 381;
  var dialoguePos = 277;
  // var directionPos = 173;
  var filename;
  var cleanLines = [];
  var cleanData = [];
  var current = null;
  var script = [];
  var regName = new RegExp('(left="' + namePos + '")(.*?)>(.*?)<');
  var regDialog = new RegExp('(left="' + dialoguePos + '")(.*?)>(.*?)<');

  // Handle file upload button
  $("#inputfile").change(function handleFileSelect(evt) {
    var f = evt.target.files[0];
    var filename = f.name.replace(/.[^.]*$/, ""); // remove file extension
    $("#list").text("Filename: " + f.name);

    createScript(inputfile, filename);

  });

  // Create the manuscript
  function createScript(inputfile, filename) {
    var reader = new FileReader();
    var file = inputfile.files[0];

    reader.onload = function() {
      var lines = this.result.split("\n");

      // Parse name and dialogue lines
      lines.forEach(function(element) {
        if (
          element.indexOf('left="' + namePos + '"') > -1 ||
          element.indexOf('left="' + dialoguePos + '"') > -1
        ) {
          cleanLines.push(element);
        }
      });


      // Create objects of name & dialogue
      cleanLines.forEach(function(entry) {
        if (entry.indexOf('left="' + namePos + '"') > -1) {
          if (!current || current.dialogue) {
            current = {
              name: regName.exec(entry)[3],
              dialogue: null,
             
            };
            script.push(current);
            return;
          }

          // Append the new name to the current name list
          current.name = `${current.name} ${regName.exec(entry)[3]}`;
          return;
        }

        // Append the new dialogue to the current dialogue list.
        // if dialogue is null, just set the dialogue as its initial value.
        var dialogue = regDialog.exec(entry)[3];
        
        // Fix HTML encoded letters
        dialogue = dialogue.replace(/&lt;/g, "[").replace(/&gt;/g, "]"); 
        dialogue = dialogue.replace(/&quot;/g, '"'); 
        current.dialogue = !current.dialogue ? dialogue : `${current.dialogue} ${dialogue}`;
       
      });

      
      // Find and mark doublets
      var findDoublets = function(){
        var originalLine = null;
        var result = script.slice(0);
        var result = _.map(script, function(object, index) {
          var match = _.find(script, function(element, ind) {          
            if (index > ind) {
              originalLine = ind;
              return _.isEqual(element, object);
            }
          })
          if (match) {
            object.isDuplicate = true;
            object.originalLine = originalLine+2;
            return object;
          } else {
            return object;
          }
        })
      }
      
      findDoublets();


      console.log("script", script)
      // Print manuscript to screen
      function showManus() {
        $("#csv").append(
          "<div class='col-md-3'><strong>CHARACTER</strong></div><div class='col-md-9'><strong>DIALOGUE</strong></div>"
        );
        script.forEach(printOut);
        function printOut(item) {
          $("#csv").append(
            "<div class='col-md-3'>" +
              item.name +
              "</div><div class='col-md-9'>" +
              item.dialogue +
              "</div>"
          );
        }
      }

      showManus();

      // Append the filename at the end of the array
      script.push({
        filename: filename
      });

      // Send array to PHP page
      function postArray(script) {
        $.ajax({
          type: "POST",
          url: "./excel.php",
          data: { json: JSON.stringify(script) },
          dataType: "json"
        });
      }

      postArray(script);

      // Activate the download button
      $("#link").attr(
        "href",
        "http://www.jenspeter.net/some2/" +
          filename +
          ".xlsx"
      );

      $("#download").show();

    };
    reader.readAsText(file);
  }
};