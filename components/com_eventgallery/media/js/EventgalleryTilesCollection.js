var EventgalleryTilesCollection = new Class({

    Implements: [Options],

    options: {
        tilesSelector: '#events-tiles .event',
        tilesContainerSelector: '#events-tiles .event-tiles'
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
        this.tiles.each(function(tile) {
            tile.setStyle('visibility', 'hidden');
            tile.setStyle('position', 'static');
            tile.setStyle('float', 'left');
        });

        // calculate tiles per row    
        var y = this.tiles[0].getPosition().y;

        for(var i=1; i<this.tiles.length; i++) {
            if (this.tiles[i].getPosition().y != y) {
                break;
            }
            tilesPerRow++;
        }
        

        // create array of height values for the columns
        var columnHeight = [];
        for (var i=0; i<tilesPerRow; i++) {
            columnHeight.push(0);
        }

        var columnWidth = this.tiles[0].getSize().x;

        this.tiles.each(function(tile) {

            var smallestColumn = this.getSmallestColumn(columnHeight);

            tile.setStyle('left', smallestColumn*columnWidth);
            tile.setStyle('top', columnHeight[smallestColumn]);

            columnHeight[smallestColumn] = columnHeight[smallestColumn]+tile.getSize().y;            

        }.bind(this));
        
        this.tiles.each(function(tile) {
            tile.setStyle('visibility', 'visible');
            tile.setStyle('position', 'absolute');
            tile.setStyle('float', 'none');
        });

        this.tilesContainer.setStyle('height', columnHeight[this.getHighestColumn(columnHeight)]);
    },
    /* 
    * returns the position of the smallest value in the array
    */	
	getSmallestColumn: function(columnHeight) {

        var smallestColumnValue = columnHeight[0];
        var smallestColumnNumber = 0;
        
        for(var i=0; i<columnHeight.length; i++) {
            if (smallestColumnValue>columnHeight[i]) {
                smallestColumnValue=columnHeight[i];
                smallestColumnNumber = i;
            }
        
        }
        return smallestColumnNumber;

    },
	/* 
    * returns the position of the highest value in the array
    */
    getHighestColumn: function(columnHeight) {

        var columnValue = columnHeight[0];
        var columnNumber = 0;
        
        for(var i=0; i<columnHeight.length; i++) {
            if (columnValue<columnHeight[i]) {
                columnValue=columnHeight[i];
                columnNumber = i;
            }
        
        }
        return columnNumber;

    }
    
});