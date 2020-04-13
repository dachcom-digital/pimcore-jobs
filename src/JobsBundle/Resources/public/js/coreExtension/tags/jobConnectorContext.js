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
        this.eventDispatcherKey = pimcore.eventDispatcher.registerTarget(this.eventDispatcherKey, this);
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
        this.component.on('destroy', function () {
            pimcore.eventDispatcher.unregisterTarget(this.eventDispatcherKey);
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
                width: 600,
                columnLines: true,
                stripeRows: true,
                columns: [
                    {
                        text: 'ID',
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
                        xtype: 'checkcolumn',
                        text: t('active'),
                        sortable: false,
                        menuText: t('Check to enable'),
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

            this.tabPanel.add(connectorPanel);

        }.bind(this));

        this.tabPanel.setActiveTab(0);

        return this.tabPanel;
    },

    postSaveObject: function () {
        Ext.Array.each(this.connectorGrids, function (connector) {
            connector['grid'].getStore().commitChanges();
        });
    },

    getValue: function () {

        var saveData = {};

        if (!this.isRendered()) {
            return null;
        }

        Ext.Array.each(this.connectorGrids, function (connector) {
            var data = connector['grid'].getStore().getRange();
            saveData[connector['connectorId']] = [];
            Ext.Array.each(data, function (record) {
                saveData[connector['connectorId']].push(record.getData());
            });
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
