/**  
 * These functions are used to manipulate select elements.
 * To move an option from one element to another, just use the "moveOption"
 * function.
 * _DONT_ forget to call "selectAll" function before submitting the form
 * when the element is a Listbox.
 *
 * @author Eduardo Santiago
 */

/**
 * Move the selected option from "source" to "destination". The element
 * can be either a Listbox or Combo.
 */
function moveOption( source, destination ) {
    var src = document.getElementById( source );
    var dst = document.getElementById( destination );
    
    for ( i = 0; i < src.length; i++ ){
        //for each selected option, move it
        if ( src.options[i].selected ){
            //put option into destination listbox
            dst.options[dst.length] = new Option( src.options[i].text, src.options[i].value );
            //remove optioon from source listbox
            src.options[i--] = null;
        }
    }
}

/**
 * Select all options from the "target" Listbox.
 * This function _MUST_ be called before submitting the form like this:
 * <form ... onSubmit='selectAll("target_listbox");'>
 * Only applies to Listbox, once Combo doesn't support multiple selected options.
 */
function selectAll( target ) {
    var tgtListbox = document.getElementById( target );
    
    //select all options of listbox to be inserted
    for ( i = 0; i < tgtListbox.length; i++ ){
        tgtListbox.options[i].selected = true;
    }
    
    js_submit_form = true;
    return true;
}
