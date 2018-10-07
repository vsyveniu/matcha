
$(function(){
 
 $( "#slider-range_age" ).slider({
 range: true,
 min: 18,
 max: 100,
 values: [ 18, 42 ],
 slide: function( event, ui ) {
 $( "#age_gap" ).val(ui.values[ 0 ] + " - " + ui.values[ 1 ] );
 }
 });
 $( "#age_gap" ).val($( "#slider-range_age" ).slider( "values", 0 ) +
 " - " + $( "#slider-range_age" ).slider( "values", 1 ) );
 
});




$(function(){
 
 $( "#slider-range_fame" ).slider({
 range: true,
 min: 0,
 max: 100,
 values: [ 24, 42 ],
 slide: function( event, ui ) {
 $( "#fame_gap" ).val(ui.values[ 0 ] + " - " + ui.values[ 1 ] );
 }
 });
 $( "#fame_gap" ).val($( "#slider-range_fame" ).slider( "values", 0 ) +
 " - " + $( "#slider-range_fame" ).slider( "values", 1 ) );
 
});