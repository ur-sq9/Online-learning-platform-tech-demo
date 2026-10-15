// QUERY JS
let query = document.getElementById('query');
query.value = query.value.replace(/[\+\*\-]/g, ''); // g means gloabl flag. So not only first matcht but replace all

// ! CHECKBOX JS - TEST - NOT IMPLEMENTED 
let hiddenInputEnroll = document.getElementById('enrolled_hidden');
let enrollCheckbox = document.getElementById('enrolled_checkbox');

enrollCheckbox.addEventListener('change', submitForm);

function submitForm(){
    hiddenInputEnroll.value = this.checked ? 'yes' : 'no'; // this = enrollCheckbox
    document.querySelector('form').submit();
    // The querySelector method in JavaScript is used to select the first HTML element that matches a specified CSS selector. 
    // If no matching element is found, it returns null.

}


// ? SUBMIT WHEN SEARCHING RATHER THAN CLICKING SUBMIT BUTTON 

let searchQuery = document.getElementById('query');


searchQuery.addEventListener('input', function(){    
        document.getElementById('search-form').submit();
});