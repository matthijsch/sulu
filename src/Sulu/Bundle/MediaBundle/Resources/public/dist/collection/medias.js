define(["mvc/collection","sulumedia/model/media"],function(a,b){return a({model:b,url:function(){return"/admin/api/media"},parse:function(a){return a._embedded.medias}})});