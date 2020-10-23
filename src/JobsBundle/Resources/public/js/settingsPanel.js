pimcore.registerNS('Jobs.SettingsPanel');
Jobs.SettingsPanel = Class.create({

    panel: null,

    initialize: function () {
        this.buildLayout();
    },

    getConfig: function () {
        return this.config;
    },

    buildLayout: function () {

        var pimcoreSystemPanel = Ext.getCmp('pimcore_panel_tabs');

        if (this.panel) {
            return this.panel;
        }

        this.panel = new Ext.Panel({
            id: 'jobs_bundle_settings',
            title: t('jobs.settings.configuration'),
            border: false,
            iconCls: 'jobs_icon_bundle',
            layout: 'border',
            closable: true,
            tbar: [
                {
                    type: 'button',
                    text: t('jobs.logs.flush_all'),
                    iconCls: 'pimcore_icon_cleanup',
                    handler: function (btn) {
                        Ext.Msg.confirm(t('jobs.logs.flush_confirm_title'), t('jobs.logs.flush_confirm'), function (confirmBtn) {

                            if (confirmBtn !== 'yes') {
                                return;
                            }

                            btn.setDisabled(true);

                            Ext.Ajax.request({
                                method: 'DELETE',
                                url: '/admin/jobs/settings/log/flush',
                                success: function (response) {

                                    btn.setDisabled(false);

                                    var resp = Ext.decode(response.responseText);
                                    if (resp.success === true) {
                                        Ext.Msg.alert(t('success'), t('jobs.logs.flush_success'));
                                    } else {
                                        Ext.Msg.alert(t('error'), resp.message);
                                    }
                                }.bind(this)
                            });
                        }.bind(this));
                    }.bind(this)
                }
            ]
        });

        this.panel.on('destroy', function () {
            pimcore.globalmanager.remove('jobs_bundle_settings');
        }.bind(this));

        pimcoreSystemPanel.add(this.panel);
        pimcoreSystemPanel.setActiveItem('jobs_bundle_settings');

        this.panel.add(new Ext.Panel({
            layout: 'fit',
            region: 'north',
            title: t('jobs.connector.context_definitions'),
            autoScroll: true,
            forceLayout: true,
            border: false,
            style: 'padding: 10px',
            items: [this.generateContextDefinitionGrid()]
        }));

        this.generateDataClassHealthCheck();
        this.addConnectors();
    },

    generateDataClassHealthCheck: function () {

        Ext.Ajax.request({
            url: '/admin/jobs/settings/data-class-health-check',
            success: function (response) {
                var config = Ext.decode(response.responseText);

                var descriptionText = !config.dataClassReady
                    ? ' ' + t('jobs.settings.dataclass.not_ready').format(config.dataClassPath)
                    : ' ' + t('jobs.settings.dataclass.active_data_class').format(config.dataClassPath);

                this.panel.add({
                    region: 'north',
                    xtype: 'fieldcontainer',
                    layout: 'fit',
                    style: 'margin: 10px',
                    items: [
                        {
                            xtype: 'label',
                            text: t('jobs.settings.dataclass.configuration') + ': ',
                        },
                        {
                            xtype: 'label',
                            text: config.dataClassReady ? t('jobs.settings.dataclass.ready_tag') : t('jobs.settings.dataclass.not_ready_tag'),
                            listeners: {
                                afterrender: function (label) {
                                    label.setStyle('color', config.dataClassReady ? '#0e793e' : '#af1e32')
                                }.bind(this)
                            }
                        },
                        {
                            xtype: 'label',
                            text: descriptionText
                        }
                    ]
                })
            }.bind(this)
        });
    },

    generateContextDefinitionGrid: function () {

        var localeStoreData = [];

        for (var i = 0; i < pimcore.settings.websiteLanguages.length; i++) {
            localeStoreData.push([pimcore.settings.websiteLanguages[i], pimcore.available_languages[pimcore.settings.websiteLanguages[i]]]);
        }

        return new Ext.grid.GridPanel({
            anchor: '100%',
            width: 500,
            columnLines: true,
            stripeRows: true,
            store: new Ext.data.JsonStore({
                autoDestroy: true,
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: '/admin/jobs/settings/list-context-definitions',
                    reader: {
                        type: 'json',
                        rootProperty: 'definitions',
                        idProperty: 'id'
                    }
                },
                fields: ['id', 'host', 'locale']
            }),
            tbar: [
                {
                    xtype: 'button',
                    text: t('add'),
                    handler: function (btn) {
                        var gridStore = btn.up('gridpanel').getStore(),
                            win = new Ext.Window({
                                width: 400,
                                height: 200,
                                modal: true,
                                title: t('jobs.connector.context.new_definition'),
                                closeAction: 'destroy',
                                items: [
                                    {
                                        xtype: 'form',
                                        bodyStyle: "padding: 10px",
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: t('jobs.connector.context.host'),
                                                name: 'host',
                                                width: 300,
                                                emptyText: 'https://www.domain.com',
                                                allowBlank: false
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: t('jobs.connector.context.locale'),
                                                name: 'locale',
                                                mode: 'local',
                                                width: 300,
                                                autoSelect: true,
                                                editable: false,
                                                allowBlank: false,
                                                store: new Ext.data.ArrayStore({
                                                    id: 0,
                                                    fields: [
                                                        'id',
                                                        'label'
                                                    ],
                                                    data: localeStoreData
                                                }),
                                                triggerAction: 'all',
                                                valueField: 'id',
                                                displayField: 'label'
                                            },
                                            {
                                                xtype: 'button',
                                                text: t('save'),
                                                iconCls: 'pimcore_icon_save',
                                                handler: function (btn) {
                                                    var form = btn.up('form');

                                                    if (!form.isValid()) {
                                                        return false;
                                                    }

                                                    form.setLoading(true);

                                                    Ext.Ajax.request({
                                                        url: '/admin/jobs/settings/create-context-definition',
                                                        method: 'POST',
                                                        params: form.getValues(),
                                                        success: function (response) {
                                                            form.setLoading(false);
                                                            var data = Ext.decode(response.responseText);
                                                            if (!data.success) {
                                                                Ext.Msg.alert(t('error'), data.message);
                                                                return;
                                                            }

                                                            win.close();
                                                            gridStore.reload();

                                                        }.bind(this)
                                                    });
                                                }.bind(this)
                                            }
                                        ]
                                    }
                                ]
                            });
                        win.show();
                    },
                    iconCls: 'pimcore_icon_add'
                }
            ],
            columns: [
                {
                    text: t('jobs.connector.context.id'),
                    sortable: false,
                    dataIndex: 'id',
                    hidden: false,
                    flex: 1
                },
                {
                    text: t('jobs.connector.context.locale'),
                    sortable: false,
                    dataIndex: 'locale',
                    hidden: false,
                    flex: 1
                },
                {
                    text: t('jobs.connector.context.host'),
                    sortable: false,
                    dataIndex: 'host',
                    flex: 2
                },
                {
                    xtype: 'actioncolumn',
                    width: 30,
                    menuText: t('jobs.connector.context.delete'),
                    hideable: false,
                    items: [{
                        tooltip: t('jobs.connector.context.delete'),
                        icon: '/bundles/pimcoreadmin/img/flat-color-icons/delete.svg',
                        handler: function (grid, rowIndex) {
                            var rec = grid.getStore().getAt(rowIndex);

                            Ext.Msg.confirm(t('delete'), t('jobs.connector.context.delete_confirm'), function (btn) {

                                if (btn !== 'yes') {
                                    return;
                                }

                                Ext.Ajax.request({
                                    url: '/admin/jobs/settings/delete-context-definition',
                                    method: 'DELETE',
                                    success: function (response) {
                                        var data = Ext.decode(response.responseText);
                                        if (!data.success) {
                                            Ext.Msg.alert(t('error'), t('error_deleting_item'));
                                            return;
                                        }

                                        grid.getStore().reload();
                                    },
                                    failure: function () {
                                        Ext.Msg.alert(t('error'), t('error_deleting_item'));
                                    },
                                    params: {id: rec.get('id')}
                                });

                            }.bind(this));
                        }.bind(this)
                    }]
                }
            ]
        });
    },

    addConnectors: function () {
        Ext.Ajax.request({
            url: '/admin/jobs/settings/get-connectors',
            success: this.buildConnectors.bind(this)
        });
    },

    buildConnectors: function (response) {
        var connectorConfig = Ext.decode(response.responseText);

        this.tabPanel = new Ext.TabPanel({
            title: t('jobs.connector.list'),
            closable: false,
            deferredRender: false,
            forceLayout: true,
            layout: 'fit',
            region: 'center',
            style: 'padding: 10px',
        });

        this.panel.add(this.tabPanel);

        Ext.Array.each(connectorConfig.connectors, function (connector) {

            var connectorLayout, connectorPanel;

            if (!Jobs.Connector.hasOwnProperty(connector.name)) {

                connectorLayout = new Jobs.Connector[ucfirst(connector.name)](connector.name, connector.config);

                connectorPanel = new Ext.Panel({
                    title: connector.label,
                    autoScroll: true,
                    forceLayout: true,
                    border: false,
                    items: [connectorLayout.getSystemFields()]
                });

                if (connectorLayout.hasFeedConfiguration() === true) {
                    connectorPanel.add(connectorLayout.generateFeedConfigurationPanel());
                }

                if (connectorLayout.hasCustomConfiguration() === true) {
                    connectorPanel.add(connectorLayout.generateCustomConfigurationPanel());
                }

                this.tabPanel.add(connectorPanel);
            }

        }.bind(this));

        this.tabPanel.setActiveTab(0);
    },

    activate: function () {
        Ext.getCmp('pimcore_panel_tabs').setActiveItem('jobs_settings');
    }
});