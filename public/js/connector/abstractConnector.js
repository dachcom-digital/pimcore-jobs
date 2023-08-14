pimcore.registerNS('Jobs.Connector.AbstractConnector');
Jobs.Connector.AbstractConnector = Class.create({

    type: null,
    data: null,

    customConfigurationPanel: null,
    feedConfigurationPanel: null,

    states: {
        installation: {
            identifier: 'installed',
            activate: t('jobs.connector.install'),
            activated: t('jobs.connector.installed'),
            inactivate: t('jobs.connector.uninstall'),
            inactivated: t('Not Installed')
        },
        availability: {
            identifier: 'enabled',
            activate: t('jobs.connector.enable'),
            activated: t('jobs.connector.enabled'),
            inactivate: t('jobs.connector.disable'),
            inactivated: t('jobs.connector.disabled')
        },
        connection: {
            identifier: 'connected',
            activate: t('jobs.connector.connect'),
            activated: t('jobs.connector.connected'),
            inactivate: t('jobs.connector.disconnect'),
            inactivated: t('jobs.connector.not_connected')
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
    hasFeedConfiguration: function () {
        return false;
    },

    /**
     * @abstract
     */
    generateFeed: function () {
        return false;
    },

    /**
     * @abstract
     */
    getCustomConfigurationFields: function (data) {
        return [];
    },

    generateFeedConfigurationPanel: function () {

        var fieldset = new Ext.form.FieldSet({
            collapsible: false,
            title: t('jobs.connector.data_feeds')
        });

        this.feedConfigurationPanel = new Ext.grid.GridPanel({
            anchor: '100%',
            width: 500,
            columnLines: true,
            stripeRows: true,
            disabled: this.data.installed === false,
            store: new Ext.data.JsonStore({
                autoDestroy: true,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: '/admin/jobs/settings/list-feed-ids/' + this.type,
                    reader: {
                        type: 'json',
                        rootProperty: 'feeds',
                        idProperty: 'internalId'
                    }
                },
                fields: ['internalId', 'externalId', 'feedUrl']
            }),
            tbar: [
                {
                    xtype: 'button',
                    text: t('jobs.connector.add_feed'),
                    iconCls: 'pimcore_icon_add',
                    handler: function (btn) {
                        this.generateFeed(btn, btn.up('gridpanel').getStore());
                    }.bind(this)
                }
            ],
            columns: [
                {
                    text: t('jobs.connector.feed.internal_id'),
                    sortable: false,
                    dataIndex: 'internalId',
                    hidden: false,
                    flex: 1
                },
                {
                    text: t('jobs.connector.feed.external_id'),
                    sortable: false,
                    dataIndex: 'externalId',
                    hidden: false,
                    flex: 1
                },
                {
                    text: t('jobs.connector.feed.feed_url'),
                    sortable: false,
                    dataIndex: 'feedUrl',
                    flex: 5
                }
            ]
        });

        fieldset.add(this.feedConfigurationPanel);

        return fieldset;
    },

    generateCustomConfigurationPanel: function () {

        var fieldset = new Ext.form.FieldSet({
            collapsible: false,
            title: t('jobs.connector.configuration')
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
            title: t('jobs.connector.system'),
            items: [
                {
                    xtype: 'fieldcontainer',
                    layout: 'hbox',
                    cls: 'install-field-container state-installation-field-container',
                    items: [
                        {
                            xtype: 'label',
                            text: t('jobs.connector.installation') + ':',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.installed ? t('jobs.connector.installed') : t('jobs.connector.not_installed'),
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
                            text: this.data.installed ? t('jobs.connector.uninstall') : t('jobs.connector.install'),
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
                            text: t('jobs.connector.status') + ':',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.enabled ? t('jobs.connector.enabled') : t('jobs.connector.disabled'),
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
                            text: this.data.enabled ? t('jobs.connector.disable') : t('jobs.connector.enable'),
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
                            text: t('jobs.connector.connection') + ':',
                            width: 100,
                        },
                        {
                            xtype: 'label',
                            width: 200,
                            cls: 'state-field-label',
                            text: this.data.autoConnect ? t('jobs.connector.auto_connected') + ': ' : (this.data.connected ? t('jobs.connector.connected') : t('jobs.connector.disconnected')),
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
                            text: this.data.connected ? t('jobs.connector.disconnect') : t('jobs.connector.connect'),
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

        Ext.Msg.confirm(t('delete'), t('jobs.connector.uninstall_note'), function (confirmBtn) {

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

        if (stateType === 'installation') {
            if (this.data.installed === false) {
                if (this.hasCustomConfiguration() === true) {
                    this.customConfigurationPanel.getForm().reset();
                    this.customConfigurationPanel.setDisabled(true);
                }
                if (this.hasFeedConfiguration() === true) {
                    this.feedConfigurationPanel.getStore().reload();
                    this.feedConfigurationPanel.setDisabled(true);
                }
            } else {
                if (this.hasCustomConfiguration() === true) {
                    this.customConfigurationPanel.setDisabled(false);
                }
                if (this.hasFeedConfiguration() === true) {
                    this.feedConfigurationPanel.setDisabled(false);
                }
            }
        }
    },

    saveCustomConfiguration: function (btn) {

        var fieldset = btn.up('panel');

        if (this.data.installed === false) {
            return;
        }

        if (this.customConfigurationPanel.getForm().isValid() === false) {
            Ext.MessageBox.alert(t('error'), t('jobs.connector.save_incorrect_configuration'));
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

                pimcore.helpers.showNotification(t('success'), t('jobs.connector.save_success'), 'success');

            }.bind(this),
            failure: function (response) {
                fieldset.setLoading(false);
            }
        });
    }
});