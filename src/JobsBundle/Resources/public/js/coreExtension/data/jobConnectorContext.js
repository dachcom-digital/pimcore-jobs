pimcore.registerNS('pimcore.object.classes.data.jobConnectorContext');
pimcore.object.classes.data.jobConnectorContext = Class.create(pimcore.object.classes.data.data, {

    type: 'jobConnectorContext',

    allowIn: {
        object: true,
        objectbrick: false,
        fieldcollection: false,
        localizedfield: false
    },

    initialize: function (treeNode, initData) {
        this.initData(initData);

        this.treeNode = treeNode;
    },

    getLayout: function ($super) {
        $super();

        this.specificPanel.removeAll();

        return this.layout;
    },

    getTypeName: function () {
        return t('jobs.connector_context');
    },

    getGroup: function () {
        return 'jobs';
    },

    getIconClass: function () {
        return 'jobs_icon_connector_context';
    }
});
