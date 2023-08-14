pimcore.registerNS('Jobs.Connector.Google');
Jobs.Connector.Google = Class.create(Jobs.Connector.AbstractConnector, {

    hasFeedConfiguration: function () {
        return false;
    },

    getConfigFields: function () {
        return []
    }
});