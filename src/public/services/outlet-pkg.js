app.factory('OultetSvc', function($q, RequestSvc, $rootScope, $ngBootbox) {

    var model = 'outlet';

    return {
        options: function(params) {
            return RequestSvc.get('/api/' + model + '/options', params);
        }
    };

});