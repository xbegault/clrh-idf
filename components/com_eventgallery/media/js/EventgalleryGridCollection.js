// create a grid layout and centers images in a tile
var EventgalleryGridCollection = new Class({
    Extends: EventgalleryToolbox,
    Implements: [Options],

    options: {
        tilesSelector: '#events-tiles .event',
        tilesContainerSelector: '#events-tiles .event-tiles',
        thumbSelector: '.event-thumbnail',
        thumbContainerSelector: '.event-thumbnails'
    },        
    tiles: null,
    tilesContainer: null,
    
    initialize: function (options) {
        this.setOptions(options);
        this.tiles = $$(this.options.tilesSelector);
        this.tilesContainer = $$(this.options.tilesContainerSelector);
        //this.calculate();
    },

    calculate: function() {

        var tilesPerRow = 1;
         // reset grid to support resize and media queries
        this.tiles.setStyles({
            visibility: 'hidden',
            position: 'static',
            'float': 'left'
        });

        // calculate tiles per row    
        var y = this.tiles[0].getPosition().y;

        for(var i=1; i<this.tiles.length; i++) {
            if (this.tiles[i].getPosition().y != y) {
                break;
            }
            tilesPerRow++;
        }


        var columnWidth = this.tiles[0].getSize().x;
        var currentColumn = 0;
        var currentRow = 0;
        
        // doing this loop multiple times increases the performance due to 
        // the fact that we can avoid size recalculations.
        this.tiles.each(function(tile) {
            tile.setStyle('left', currentColumn*columnWidth);
            tile.setStyle('top', currentRow*columnWidth);                        
            tile.setStyle('height', columnWidth);
            
            currentColumn++;
            if (currentColumn+1>tilesPerRow) {
                currentColumn = 0;                
                currentRow++;
            }
            
            
        }.bind(this));
        
        // calculate center images date
        this.tiles.each(function(tile) {  
            var thumb = tile.getElements(this.options.thumbSelector)[0];
            var thumbContainer = tile.getElements(this.options.thumbContainerSelector)[0];
            var tileSize = tile.getSize()
            
            var thumbSize = thumb.getSize();
            
            var tileWidth = tileSize.x - this.calcBorderWidth([tile], ['padding-right', 'margin-right', 'border-width', 'padding-left', 'margin-left', 'border-width']);
            
            var adjustX = Math.floor((tileWidth - thumbSize.x)/2);
            var adjustY = Math.floor((tileSize.y - thumbSize.y)/2);
            
            thumbContainer.setAttribute('data-adjust-x', adjustX);
            thumbContainer.setAttribute('data-adjust-y', adjustY);            
        }.bind(this));
        
        // center images
        this.tiles.each(function(tile) {  
        	var thumbContainer = tile.getElements(this.options.thumbContainerSelector)[0];
            thumbContainer.setStyle('left', thumbContainer.getAttribute('data-adjust-x')+'px' );
            thumbContainer.setStyle('top',  thumbContainer.getAttribute('data-adjust-y')+'px' );
        }.bind(this));        

        var overallHeight = Math.ceil(this.tiles.length/tilesPerRow)*columnWidth;
        this.tilesContainer.setStyle('height', overallHeight);

        this.tiles.setStyles({
            visibility: 'visible',
            position: 'absolute',
            'float': 'none'
        });

    }

});