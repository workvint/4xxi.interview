$(document).ready(function() {
    var DCollectionView = Backbone.View.extend({
        initialize: function() {
            this.$div = this.$el.find('div[data-prototype]');
            this.widget = this.$div.attr('data-prototype');
            this.widgetId = this.$div.children().length; 
        },
        
        events: {
            'click button.dcollection_add_item' : 'add_item',
            'click button.dcollection_remove_item' : 'remove_item'
        },
        
        add_item: function() {
            this.render();
        },
       
        remove_item: function(event) {
           $(event.currentTarget).parents().get(2).remove();
        },
        
        render: function() {
            this.$div.append(this.widget.replace(/__name__/g, this.widgetId++));
        }
    });
    
    var AppView = Backbone.View.extend({
        el: 'body',
        
        initialize: function() {
            $('div[data-prototype]').each(function() {
               new DCollectionView({'el': $(this).parents('form')}); 
            });
        }
    });
    
    var App = new AppView();
});
