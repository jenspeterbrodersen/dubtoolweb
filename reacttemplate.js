import ReactDOM from 'react-dom';
import styled from 'styled-components';
import { applyContainerQuery } from 'react-container-query';
import _has from 'lodash/has';
import Webpart from '../../Webpart';
import { theme } from '../../Theme';
import AddEditContainer, { AddEditButton } from '../../AddEditComponents';

const BannerWrapper = styled.div.attrs({
  style: props => ({
    backgroundImage: props.bannerUrl ? 'url(' + props.bannerUrl + ')' : null,
  }),
})`
  position: relative;
  width: 100%;
  height: 91px;
  border-radius: 5px;
  background-color: #f3c24d;
  box-sizing: border-box;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: 50% 50%;
  background-image: ${props => props.backgroundImage};
  &.isMobile {
    display: none;
    height: 0px;
  }
`;

const Text = styled.div`
  color: #fff !important;
  font-family: Helvetica !important;
  font-size: 24px !important;
  font-weight: normal !important;
  position: absolute;
  bottom: 20px;
  left: 20px;
  letter-spacing: 1px;
  span {
    font-weight: bold !important;
  }
`;

export default class Banner extends Webpart {
  constructor() {
    super();
    this.skipMaxWidth = true;
    this.currentBreakpoint = null;
    this.state = {
      data: {},
    };
  }

  listsLoaded(data) {
    this.setState({
      data,
    });

    this.stopLoading();
  }

  render() {
    const { componentData, data, isLoaded } = this.state;

    const loadConfig = _has(componentData, 'Title');
    const listTitle = _has(componentData, 'WebpartList.results[0].Title') ? componentData.WebpartList.results[0].Title : '';
    const loadData = _has(data, 'Title');
    const webpartTitle = loadConfig ? componentData.Title : '';

    const addEditFields = {
      addFields: [
        {
          Name: 'Webpart ID',
          Type: 'select',
          Field: 'select[title="Webpart ID"]',
          Value: webpartTitle,
          Hide: true,
        },
        {
          Name: 'Site',
          Type: 'select',
          Field: 'select[title="Site"]',
          Value: false,
          Hide: true,
        },
      ],
      editFields: [
        {
          Name: 'Webpart ID',
          Type: '',
          Field: 'select[title="Webpart ID"]',
          Value: false,
          Hide: true,
        },
        {
          Name: 'Site',
          Type: '',
          Field: 'select[title="Site"]',
          Value: false,
          Hide: true,
        },
      ],
    };

    const configAddEditFields = {
      addFields: [
        {
          Name: 'Webpart ID',
          Type: 'input',
          Field: 'input[title="Webpart ID"]',
          Value: this.props.webpartId,
          Hide: true,
        },
        {
          Name: 'Webpart Color',
          Type: '',
          Field: 'select[title="Webpart Color"]',
          Value: false,
          Hide: true,
        },
        {
          Name: 'Webpart Config',
          Type: '',
          Field: 'textarea[title="Webpart Config"]',
          Value: false,
          Hide: true,
        },
        {
          Name: 'Site',
          Type: 'select',
          Field: 'select[title="Site"]',
          Value: false,
          Hide: true,
        },
      ],
      editFields: [
        {
          Name: 'Webpart ID',
          Type: '',
          Field: 'input[title="Webpart ID"]',
          Value: false,
          Hide: true,
        },
        {
          Name: 'Webpart Color',
          Type: '',
          Field: 'select[title="Webpart Color"]',
          Value: false,
          Hide: true,
        },
        {
          Name: 'Webpart List',
          Type: '',
          Field: '[id$="MultiLookupPicker"]',
          Value: false,
          Hide: true,
        },
        {
          Name: 'Webpart Config',
          Type: '',
          Field: 'textarea[title="Webpart Config"]',
          Value: false,
          Hide: true,
        },
        {
          Name: 'Site',
          Type: '',
          Field: 'select[title="Site"]',
          Value: false,
          Hide: true,
        },
      ],
    };

    return (
      <BannerWrapper
        isLoaded={isLoaded}
        bannerUrl={loadData ? data.ImageUrl : false}
        className={this.props.containerQuery.mobilePortrait ? 'isMobile' : 'noMobile'}
      >
        {this.checkEditMode() && (
          <AddEditContainer>
            {loadConfig ? (
              <span>
                <AddEditButton
                  onClick={() => this.openAddEditModal(LIST_CONFIG, configAddEditFields, componentData.Id)}
                  value="Edit Config"
                />
                {loadData ? (
                  <AddEditButton onClick={() => this.openAddEditModal(listTitle, addEditFields, data.Id)} value="Edit" />
                ) : (
                  <AddEditButton className="add" onClick={() => this.openAddEditModal(listTitle, addEditFields)} value="Add" />
                )}
              </span>
            ) : (
              <AddEditButton
                className="add"
                onClick={() => this.openAddEditModal(LIST_CONFIG, configAddEditFields)}
                value="Add Config"
              />
            )}
          </AddEditContainer>
        )}
        {loadData &&
          data.ShowTitle && (
          <Text>
            <span>{data.Title}</span> {data.SubTitle}
          </Text>
        )}
      </BannerWrapper>
    );
  }
}

Banner.renderWebparts();
