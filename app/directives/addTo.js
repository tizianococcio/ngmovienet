app.directive('addTo', function() {
  return {
    restrict: 'EA',
    //require: ['^ngModel'],
    replace: true,
    scope: {
      ngModel: '=',
    },
    //templateUrl: 'views/nprListItem.html',
    template: '<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown" ng-click="play()">Aggiungi a <b class="caret"></b></a>',
    link: function(scope, ele, attr) {

    }
  }
});