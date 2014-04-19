angular.module('ng-remote-validator-directive', [])
 .directive('ngValidazione', ['$http', '$timeout', function ($http, $timeout) {
  return {
      restrict: 'A',
      require: '^ngModel',
      link: function( scope, el, attrs, ngModel ) {
          var cache = {},
              handleChange,
              setValidation,
              addToCache,
              originalValue,
              request,
              shouldProcess,
              options = {
                  ngRemoteThrottle: 4,
                  ngRemoteMethod: 'POST'
              };

          angular.extend( options, attrs );

          addToCache = function( data ) {
              if ( cache[ data.value ] ) return;
              cache[ data.value ] = data.valid;
          };

          shouldProcess = function( value ) {
              var otherRulesInValid = false;
              for ( var p in ngModel.$error ) {
                  if ( ngModel.$error[ p ] && p != 'ngValidazione' ) {
                      otherRulesInValid = true;
                      break;
                  }
              }
              return !( ngModel.$pristine || value === originalValue || otherRulesInValid );
          };

          setValidation = function( data ) {
              ngModel.$setValidity( 'ngRemoteError', data.isValid );  
              addToCache( data );
              el.removeClass( 'ng-processing' );
          };

          handleChange = function( value ) {

              originalValue = originalValue || value;

              if ( !shouldProcess( value ) ) {
                  return setValidation( { isValid: true, value: value } );
              }

              if ( cache[ value ] ) {
                  return setValidation( cache[ value ] );
              }

              if ( request ) {
                  $timeout.cancel( request );
              }

              request = $timeout( function( ) {

                  el.addClass( 'ng-processing' );
                  $http( { method: options.ngRemoteMethod, url: options.ngValidazione, data: { value: value } } ).success( setValidation );
              }, options.ngRemoteThrottle );
              return true;
          };

          scope.$watch( function( ) {
              return ngModel.$viewValue;
          }, handleChange );
      }
  };
 }]);