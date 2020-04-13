pimcore.registerNS('Jobs.Connector.Facebook');
Jobs.Connector.Facebook = Class.create(Jobs.Connector.AbstractConnector, {

    hasCustomConfiguration: function () {
        return true;
    },

    connectHandler: function (stateType, mainBtn) {

        var stateData = this.states[stateType],
            token = this.data.token,
            flag = this.data[stateData.identifier] === true ? 'deactivate' : 'activate';

        // just go by default
        if (flag === 'deactivate') {
            this.stateHandler(stateType, mainBtn);
            return;
        }

        if (this.data.token === null) {
            Ext.MessageBox.alert(t('error'), 'No valid token found. Please install this connector first.');
            return;
        }

        mainBtn.setDisabled(true);

        var win = new Ext.Window({
            width: 400,
            bodyStyle: 'padding:10px',
            title: t('Facebook Connect Service'),
            html: t('You need to connect or application. first click "Connect". If the connection was successful, click "Check & Apply Connection"'),
            listeners: {
                beforeclose: function () {
                    mainBtn.setDisabled(false);
                }
            },
            buttons: [{
                text: t('Connect'),
                iconCls: 'pimcore_icon_open_window',
                handler: function (btn) {
                    var buttons = btn.up('window').query('button');
                    buttons[1].setDisabled(false);
                    btn.setDisabled(true);
                    // use http://localhost:2332 or something in env context
                    window.open('http://localhost:2332/jobs/facebook/' + token + '/connect', '_blank');
                }
            }, {
                text: t('Check & Apply Connection'),
                iconCls: 'pimcore_icon_apply',
                disabled: true,
                handler: function () {
                    win.close();
                    this.stateHandler('connection', mainBtn);
                }.bind(this)
            }]
        });

        win.show();
    },

    getCustomConfigurationFields: function (data) {

        return [
            {
                xtype: 'textfield',
                name: 'appId',
                fieldLabel: 'App ID',
                allowBlank: false,
                value: data.hasOwnProperty('appId') ? data.appId : null
            },
            {
                xtype: 'textfield',
                name: 'appSecret',
                fieldLabel: 'App Secret',
                allowBlank: false,
                value: data.hasOwnProperty('appSecret') ? data.appSecret : null
            },
            {
                xtype: 'textfield',
                name: 'publisherName',
                fieldLabel: 'The name of your organization',
                allowBlank: false,
                value: data.hasOwnProperty('publisherName') ? data.publisherName : null
            },
            {
                xtype: 'textfield',
                name: 'publisherUrl',
                fieldLabel: 'The URL of your organizations\'s website',
                allowBlank: false,
                value: data.hasOwnProperty('publisherUrl') ? data.publisherUrl : null
            },
            {
                xtype: 'textfield',
                name: 'RecruitingManagerId',
                fieldLabel: 'Recruiting Manager User ID',
                allowBlank: false,
                value: data.hasOwnProperty('RecruitingManagerId') ? data.RecruitingManagerId : null
            },
            {
                xtype: 'textfield',
                name: 'photoUrl',
                fieldLabel: 'Photo Url',
                allowBlank: false,
                value: data.hasOwnProperty('photoUrl') ? data.photoUrl : null
            },
            {
                xtype: 'textfield',
                name: 'dataPolicyUrl',
                fieldLabel: 'Data Policy Url',
                allowBlank: false,
                value: data.hasOwnProperty('dataPolicyUrl') ? data.dataPolicyUrl : null
            },
        ];
    }
});