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
            title: 'Jobs Bundle Connectors',
            autoScroll: true,
            forceLayout: true,
            border: false,
            style: 'padding: 10px',
            items: [
                {
                    xtype: 'label',
                    style: 'margin: 10px; display: inline-block;',
                    text: 'This Bundle is currently under heavy development!'
                }
            ]
        }));

        this.addConnectors();
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
            title: false,
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