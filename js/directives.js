var app = angular.module("DhakDirectives", []);

app.directive("dhAlert", function() {
  return function(scope, element, attrs) {
    if (scope.unite.nb_chef == 0) {
        element.addClass("danger");
       
        element.find('span').addClass("glyphicon");
        element.find('span').addClass("glyphicon-warning-sign");
        element.find('span').after("&nbsp;Maitrise non saisie");
    } else if ( (scope.unite.size == 0) || (scope.unite.nb_sizaine == "") ){
        element.addClass("warning");
       
        element.find('span').addClass("glyphicon");
        element.find('span').addClass("glyphicon-warning-sign");
        element.find('span').after("&nbsp;Taille non saisie");
  	}
  }
});