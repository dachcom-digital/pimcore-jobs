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
            title: t('Jobs Configuration'),
            border: false,
            iconCls: 'jobs_icon_bundle',
            layout: 'border',
            closable: true,
        });

        this.panel.on('destroy', function () {
            Formbuilder.eventObserver = null;
            pimcore.globalmanager.remove('jobs_bundle_settings');
        }.bind(this));

        pimcoreSystemPanel.add(this.panel);
        pimcoreSystemPanel.setActiveItem('jobs_bundle_settings');

        this.panel.add(new Ext.Panel({
            layout: 'fit',
            region: 'north',
            title: 'Context Definitions',
            autoScroll: true,
            forceLayout: true,
            border: false,
            style: 'padding: 10px',
            items: [this.generateContextDefinitionGrid()]
        }));

        this.addConnectors();
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
                                title: t('New Context Definition'),
                                closeAction: 'destroy',
                                items: [
                                    {
                                        xtype: 'form',
                                        bodyStyle: "padding: 10px",
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: t('Host'),
                                                name: 'host',
                                                width: 300,
                                                emptyText: 'https://www.domain.com',
                                                allowBlank: false
                                            },
                                            {
                                                xtype: 'combobox',
                                                fieldLabel: t('Locale'),
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
                    text: t('Id'),
                    sortable: false,
                    dataIndex: 'id',
                    hidden: false,
                    flex: 1
                },
                {
                    text: t('locale'),
                    sortable: false,
                    dataIndex: 'locale',
                    hidden: false,
                    flex: 1
                },
                {
                    text: t('host'),
                    sortable: false,
                    dataIndex: 'host',
                    flex: 2
                },
                {
                    xtype: 'actioncolumn',
                    width: 30,
                    menuText: t('delete'),
                    hideable: false,
                    items: [{
                        tooltip: t('delete'),
                        icon: '/bundles/pimcoreadmin/img/flat-color-icons/delete.svg',
                        handler: function (grid, rowIndex) {
                            var rec = grid.getStore().getAt(rowIndex);

                            Ext.Msg.confirm(t('delete'), t('Do you really want to delete this context definition? Every linked Job Context will be removed too!'), function (btn) {

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
            title: t('Connectors'),
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