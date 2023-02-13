pimcore.registerNS('pimcore.object.tags.jobConnectorContext');
pimcore.object.tags.jobConnectorContext = Class.create(pimcore.object.tags.abstract, {

    type: 'jobConnectorContext',

    data: null,

    contextDefinitions: null,
    connectors: null,
    connectorGrids: [],

    layoutPanel: null,
    settingsPanel: null,

    initialize: function (data, fieldConfig) {
        this.connectorGrids = [];
        this.data = data.context;
        this.contextDefinitions = data.context_definitions;
        this.connectors = data.connectors;
        this.fieldConfig = fieldConfig;
        document.addEventListener(pimcore.events.postSaveObject, this.postSaveObject.bind(this));
    },

    getGridColumnConfig: function (field) {
        return {
            header: ts(field.label), width: 150, sortable: false, dataIndex: field.key,
            renderer: function (key, value, metaData, record) {
                this.applyPermissionStyle(key, value, metaData, record);
                return t('not_supported');
            }.bind(this, field.key)
        };
    },

    getLayoutShow: function () {
        this.component = this.getLayoutEdit();
        this.component.on('afterrender', function () {
            this.component.disable();
        }.bind(this));

        return this.component;
    },

    getName: function () {
        return this.fieldConfig.name;
    },

    getLayoutEdit: function () {
        this.component = this.getEditLayout();
        this.component.on('destroy', function() {
            document.removeEventListener(pimcore.events.postSaveObject, this.postSaveObject.bind(this));
        }.bind(this));

        return this.component;
    },

    getEditLayout: function () {

        if (this.layoutPanel !== null) {
            return this.layoutPanel;
        }

        this.layoutPanel = new Ext.Panel({
            cls: 'object_field',
            iconCls: 'jobs_icon_connector_context',
            title: this.getTitle(),
            items: this.getItems(),
        });

        return this.layoutPanel;
    },

    findStateForItem: function (connectorId, contextId) {

        var state = false;
        if (!Ext.isArray(this.data)) {
            return false;
        }

        Ext.Array.each(this.data, function (item) {
            if (item.connector.id === connectorId && item.contextDefinitionId === contextId) {
                state = true;
                return false;
            }
        });

        return state;
    },

    getItems: function () {

        this.tabPanel = new Ext.TabPanel({
            title: false,
            closable: false,
            layout: 'border',
            style: 'padding: 10px',
        });

        Ext.Array.each(this.connectors, function (connector) {

            var connectorPanel, contextDefinitionGrid, storeFields, storeData = [];

            storeFields = [
                {name: 'id'},
                {name: 'locale'},
                {name: 'host'},
                {name: 'active'}
            ];

            Ext.Array.each(this.contextDefinitions, function (contextDefinition) {
                storeData.push([
                    contextDefinition.id,
                    contextDefinition.locale,
                    contextDefinition.host,
                    this.findStateForItem(connector.id, contextDefinition.id)
                ])
            }.bind(this));

            contextDefinitionGrid = new Ext.grid.GridPanel({
                store: new Ext.data.SimpleStore({
                    fields: storeFields,
                    data: storeData
                }),
                flex: 1,
                width: 800,
                columnLines: true,
                stripeRows: true,
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
                        xtype: 'checkcolumn',
                        text: t('jobs.connector.context.active'),
                        sortable: false,
                        menuText: t('jobs.connector.context.check_to_enable'),
                        width: 100,
                        dataIndex: 'active',
                    }
                ]
            });

            this.connectorGrids.push({
                connectorName: connector.name,
                connectorId: connector.id,
                grid: contextDefinitionGrid
            });

            connectorPanel = new Ext.Panel({
                title: connector.label,
                border: false,
                layout: 'form',
                anchor: '100%',
                items: contextDefinitionGrid
            });

            if (connector.has_log_panel === true) {
                connectorPanel.add(this.generateLogPanel(connector.id));
            }

            this.tabPanel.add(connectorPanel);

        }.bind(this));

        this.tabPanel.setActiveTab(0);

        return this.tabPanel;
    },

    generateLogPanel: function (connectorEngineId) {

        var store, grid, bbar, itemsPerPage = pimcore.helpers.grid.getDefaultPageSize(-1);

        store = new Ext.data.Store({
            pageSize: itemsPerPage,
            proxy: {
                type: 'ajax',
                url: '/admin/jobs/settings/log/get-for-object/' + connectorEngineId + '/' + (this.object.id),
                reader: {
                    type: 'json',
                    rootProperty: 'entries'
                },
                extraParams: {
                    formId: this.formId
                }
            },
            autoLoad: false,
            fields: ['id', 'type', 'message', 'date']
        });

        bbar = pimcore.helpers.grid.buildDefaultPagingToolbar(store, {pageSize: itemsPerPage});

        Ext.Array.each(bbar.query('tbtext'), function (tbTextComp) {
            tbTextComp.setStyle({
                fontSize: 'inherit !important',
                lineHeight: 'inherit !important'
            });
        });

        grid = new Ext.grid.GridPanel({
            flex: 1,
            width: 800,
            style: {
                marginTop: '20px',
            },
            store: store,
            border: true,
            columnLines: true,
            stripeRows: true,
            title: false,
            bodyCls: 'pimcore_editable_grid',
            tbar: [
                {
                    xtype: 'label',
                    text: t('Logs')
                },
                '->',
                {
                    type: 'button',
                    text: t('cleanup'),
                    iconCls: 'pimcore_icon_cleanup',
                    handler: function () {
                        Ext.Ajax.request({
                            method: 'DELETE',
                            url: '/admin/jobs/settings/log/remove-for-object/' + connectorEngineId + '/' + (this.object.id),
                            success: function (response) {
                                store.reload();
                            }.bind(this)
                        });
                    }.bind(this)
                }
            ],
            bbar: bbar,
            listeners: {
                afterrender: function () {
                    store.load();
                }
            },
            columns: [
                {text: 'ID', sortable: false, dataIndex: 'id', hidden: true},
                {text: t('type'), sortable: false, dataIndex: 'type', hidden: false},
                {text: t('message'), sortable: false, dataIndex: 'message', flex: 3, renderer: Ext.util.Format.htmlEncode},
                {text: t('date'), sortable: false, dataIndex: 'date', flex: 1},
            ]
        });

        return grid;

    },

    postSaveObject: function () {
        Ext.Array.each(this.connectorGrids, function (connector) {
            connector['grid'].getStore().commitChanges();
        });
    },

    getValue: function () {

        var saveData = [];

        if (!this.isRendered()) {
            return null;
        }

        Ext.Array.each(this.connectorGrids, function (connector) {
            var connectorData = connector.grid.getStore().getRange(),
                connectorStorageData = {
                    connectorId: connector.connectorId,
                    connectorName: connector.connectorName,
                    contextItems: []
                };

            Ext.Array.each(connectorData, function (record) {
                if (record.get('active') === true) {
                    connectorStorageData.contextItems.push(record.getData());
                }
            });

            saveData.push(connectorStorageData);
        });

        return saveData;
    },

    isDirty: function () {

        var hasModifiedRecords = false;

        if (this.connectorGrids.length === 0) {
            return false;
        }

        Ext.Array.each(this.connectorGrids, function (connector) {
            if (connector['grid'].getStore().getModifiedRecords().length > 0) {
                hasModifiedRecords = true;
            }
        });

        return hasModifiedRecords;
    }
});
