app.factory('OutletSvc', function($q, RequestSvc, $rootScope, $ngBootbox) {

    var model = 'outlet';

    return {
        index: function(params) {
            return RequestSvc.get('/api/' + model + '/index', params);
        },
        read: function(id) {
            return RequestSvc.get('/api/' + model + '/read/' + id);
        },
        getBusiness: function(params) {
            return RequestSvc.post('/api/' + model + '/get-business', params);
        },

        save: function(params) {
            return RequestSvc.post('/api/' + model + '/save', params);
        },
        saveFromNgData: function(params) {
            return RequestSvc.post('/api/' + model + '/save-from-ng-data', params);
        },
        remove: function(params) {
            return RequestSvc.post('api/' + model + '/delete', params);
        },
        options: function(params) {
            return RequestSvc.get('/api/' + model + '/options', params);
        },
    };
});