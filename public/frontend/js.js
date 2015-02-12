/**
 * Created by miroslawratman on 11/02/15.
 */
$( document ).ready(function() {
    $('#dbtablenameAll-all').on('click', function(){
        $('#dbtablename-element input.multi-checkbox').prop('checked', this.checked);
    })
});