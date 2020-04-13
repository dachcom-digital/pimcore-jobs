pimcore.registerNS('Jobs.Connector.AbstractConnector');
Jobs.Connector.AbstractConnector = Class.create({

    type: null,
    data: null,

    customConfigurationPanel: null,

    states: {
        installation: {
            identifier: 'installed',
            activate: t('Install'),
            activated: t('Installed'),
            inactivate: t('Uninstall'),
            inactivated: t('Not Installed')
        },
        availability: {
            identifier: 'enabled',
            activate: t('Enable'),
            activated: t('Enabled'),
            inactivate: t('Disable'),
            inactivated: t('Disabled')
        },
        connection: {
            identifier: 'connected',
            activate: t('Connect'),
            activated: t('Connected'),
            inactivate: t('Disconnect'),
            inactivated: t('Not connected')
        },
    },

    initialize: function (type, data) {
        this.type = type;
        this.data = data;
    },

    getType: function () {
        return this.type;
    },

    /**
     * @abstract
     */
    hasCustomConfiguration: function () {
        return false;
    },

    /**
     * @abstract
     */
    getCustomConfigurationFields: function (data) {
        return [];
    },

    generateCustomConfigurationPanel: function () {

        var fieldset = new Ext.form.FieldSet({
            collapsible: false,
            title: t('Connector Configuration')
        }), data = this.data.customConfiguration !== null ? this.data.customConfiguration : {};

        this.customConfigurationPanel = new Ext.form.Panel({
            title: false,
            layout: 'form',
            border: false,
            autoScroll: true,
            width: 600,
            disabled: this.data.installed === false,
            items: this.getCustomConfigurationFields(data),
            buttons: [
                {
                    text: t('save'),
                    iconCls: 'pimcore_icon_save',
                    handler: this.saveCustomConfiguration.bind(this)
                }
            ]
        });

        fieldset.add(this.customConfigurationPanel);

        return fieldset;
    },

    getSystemFields: function () {

        return {
            xtype: 'fieldset',
            collapsible: false,
            title: 'System',
            items: [
                {
                    xtype: 'fieldcontainer',
                    layout: 'hbox',
                    cls: 'install-field-container state-installation-field-container',
                    items: [
                        {
                            xtype: 'label',
                            text: 'Installation:',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.installed ? t('Installed') : t('Not installed'),
                            listeners: {
                                afterrender: function (label) {
                                    label.setStyle('color', this.data.installed ? '#0e793e' : '#af1e32')
                                }.bind(this)
                            },
                        },
                        {
                            xtype: 'button',
                            width: 150,
                            iconCls: this.data.installed ? 'pimcore_icon_cancel' : 'pimcore_icon_add',
                            text: this.data.installed ? t('Uninstall') : t('Install'),
                            style: 'border-color: transparent;',
                            listeners: {
                                afterrender: function (btn) {
                                    btn.setStyle('background-color', this.data.installed ? '#af1e32' : '#0e793e')
                                }.bind(this)
                            },
                            handler: this.installHandler.bind(this)
                        },
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    disabled: !this.data.installed,
                    cls: 'state-field-container state-availability-field-container',
                    layout: 'hbox',
                    items: [
                        {
                            xtype: 'label',
                            text: 'Status:',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.enabled ? t('Enabled') : t('Disabled'),
                            listeners: {
                                afterrender: function (label) {
                                    label.setStyle('color', this.data.enabled ? '#0e793e' : '#af1e32')
                                }.bind(this)
                            },
                        },
                        {
                            xtype: 'button',
                            width: 150,
                            iconCls: this.data.enabled ? 'pimcore_icon_cancel' : 'pimcore_icon_add',
                            text: this.data.enabled ? t('Disable') : t('Enable'),
                            style: 'border-color: transparent;',
                            listeners: {
                                afterrender: function (btn) {
                                    btn.setStyle('background-color', this.data.enabled ? '#af1e32' : '#0e793e')
                                }.bind(this)
                            },
                            handler: this.stateHandler.bind(this, 'availability')
                        }
                    ]
                },
                {
                    xtype: 'fieldcontainer',
                    disabled: (!this.data.installed || this.data.autoConnect === true),
                    cls: 'state-field-container ' + (this.data.autoConnect === false ? 'state-connection-field-container' : ''),
                    layout: 'hbox',
                    items: [
                        {
                            xtype: 'label',
                            text: 'Connection:',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.autoConnect ? t('Auto Connected') : (this.data.connected ? t('Connected') : t('Currently Disconnected')),
                            listeners: {
                                afterrender: function (label) {
                                    var color = this.data.autoConnect ? '#212121' : (this.data.connected ? '#0e793e' : '#af1e32');
                                    label.setStyle('color', color);
                                }.bind(this)
                            }
                        },
                        {
                            xtype: 'button',
                            width: 150,
                            hidden: this.data.autoConnect,
                            iconCls: this.data.connected ? 'pimcore_icon_cancel' : 'pimcore_icon_add',
                            text: this.data.connected ? t('Disconnect') : t('Connect'),
                            style: 'border-color: transparent;',
                            listeners: {
                                afterrender: function (btn) {
                                    var color = this.data.autoConnect ? '#505050' : (this.data.connected ? '#af1e32' : '#0e793e');
                                    btn.setStyle('background-color', color);
                                }.bind(this)
                            },
                            handler: this.connectHandler.bind(this, 'connection')
                        }
                    ]
                }
            ]
        }
    },

    installHandler: function (btn) {

        var url = this.data.installed ? '/admin/jobs/settings/uninstall-connector/' : '/admin/jobs/settings/install-connector/',
            fieldset = btn.up('fieldset'),
            doRequest = function (btn) {

                btn.setDisabled(true);

                Ext.Ajax.request({
                    url: url + this.type,
                    success: function (response) {
                        var resp = Ext.decode(response.responseText);

                        btn.setDisabled(false);

                        if (resp.success === false) {
                            Ext.MessageBox.alert(t('error'), resp.message);
                            return;
                        }

                        this.data.installed = resp.installed;
                        this.data.token = resp.token;

                        if (this.data.installed === false) {
                            this.data.enabled = false;
                            this.data.connected = false;
                        }

                        this.changeState(fieldset, 'installation');
                        this.changeState(fieldset, 'connection');
                        this.changeState(fieldset, 'availability');

                    }.bind(this),
                    failure: function (response) {
                        btn.setDisabled(false);
                    }
                });
            }.bind(this);

        if (this.data.installed === false) {
            doRequest(btn);
            return;
        }

        Ext.Msg.confirm(t('delete'), t('Do you really want to uninstall this connector? Every linked Job Context will be removed too!'), function (confirmBtn) {

            if (confirmBtn !== 'yes') {
                return;
            }

            doRequest(btn);
        });
    },

    connectHandler: function (stateType, btn) {
        // connectHandler: Just a proxy to allow connectors overriding connecting process!
        this.stateHandler(stateType, btn);
    },

    stateHandler: function (stateType, btn) {

        var stateData = this.states[stateType],
            fieldset = btn.up('fieldset'), flag, url;

        flag = this.data[stateData.identifier] === true ? 'deactivate' : 'activate';
        url = '/admin/jobs/settings/change-connector-type/' + this.type + '/' + stateType + '/' + flag;

        btn.setDisabled(true);

        Ext.Ajax.request({
            url: url,
            success: function (response) {
                var resp = Ext.decode(response.responseText);

                btn.setDisabled(false);

                if (resp.success === false) {
                    Ext.MessageBox.alert(t('error'), resp.message);
                    return;
                }

                this.data[stateData.identifier] = resp.stateMode === 'activated';

                this.changeState(fieldset, stateType)

            }.bind(this),
            failure: function (response) {
                btn.setDisabled(false);
            }
        });
    },

    changeState: function (fieldset, stateType) {

        var fieldContainer = fieldset.query('fieldcontainer[cls*="state-' + stateType + '-field-container"]')[0],
            stateLabelField = fieldContainer ? fieldContainer.query('label[cls*="state-field-label"]')[0] : null,
            btn = fieldContainer ? fieldContainer.query('button')[0] : null,
            stateData = this.states[stateType],
            active = this.data.installed === false ? false : this.data[stateData.identifier];

        if (stateLabelField !== null) {
            stateLabelField.setText(active ? stateData.activated : stateData.inactivated);
            stateLabelField.setStyle('color', active ? '#0e793e' : '#af1e32');
        }

        if (btn !== null) {
            btn.setText(active ? stateData.inactivate : stateData.activate);
            btn.setStyle('background-color', active ? '#af1e32' : '#0e793e');
            btn.setIconCls(active ? 'pimcore_icon_cancel' : 'pimcore_icon_add');
        }

        if (stateType !== 'installation' && fieldContainer) {
            fieldContainer.setDisabled(!this.data.installed);
        }

        if (stateType === 'installation' && this.hasCustomConfiguration() === true) {
            if (this.data.installed === false) {
                this.customConfigurationPanel.getForm().reset();
                this.customConfigurationPanel.setDisabled(true);
            } else {
                this.customConfigurationPanel.setDisabled(false);
            }
        }
    },

    saveCustomConfiguration: function (btn) {

        var fieldset = btn.up('panel');

        if (this.data.installed === false) {
            return;
        }

        if (this.customConfigurationPanel.getForm().isValid() === false) {
            Ext.MessageBox.alert(t('error'), t('Configuration is not valid. Please fill out all required fields.'));
            return;
        }

        fieldset.setLoading(true);

        Ext.Ajax.request({
            url: '/admin/jobs/settings/save-connector-configuration/' + this.type,
            method: 'POST',
            params: {
                configuration: Ext.encode(this.customConfigurationPanel.getForm().getValues())
            },
            success: function (response) {
                var resp = Ext.decode(response.responseText);

                fieldset.setLoading(false);

                if (resp.success === false) {
                    Ext.MessageBox.alert(t('error'), resp.message);
                    return;
                }

                pimcore.helpers.showNotification(t('success'), t('Connector Configuration successfully saved.'), 'success');

            }.bind(this),
            failure: function (response) {
                fieldset.setLoading(false);
            }
        });
    }
});