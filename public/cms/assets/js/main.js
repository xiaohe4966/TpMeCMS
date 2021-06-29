/*--------------------
// App Name: Evora - Real Estate HTML Template
// Author: Wicombit
// Author URI: https://themeforest.net/user/wicombit/portfolio
// Version: 1.00
--------------------*/

/* PageLoader */

'use strict';
$(window).on('load', function() {
   $('#preloader').fadeOut('slow');
});

/* Nice Select */

'use strict';
$(document).ready(function() {
	$('.nc-select').niceSelect();
});

/* DateRangePicker */

'use strict';
$(document).ready(function() {
  
  $('input[name="date"]').daterangepicker({
    singleDatePicker: true,
    autoApply: true,
    linkedCalendars: false,
    showCustomRangeLabel: false,
  })
});

/* Mortage Calculator */

function calculateMortgage(p,r,n) {

  r = percentToDecimal(r);

  n = yearsToMonths(n);

  var pmt = ( r * p ) / (1 - Math.pow((1 + r) , (-n) ));

  return parseFloat(pmt.toFixed(2));
}

function percentToDecimal(percent) {
  return (percent/12)/100;
}

function yearsToMonths(year) {
  return year * 12;
}

function postPayments(payment) {
  document.getElementById("inResults").style.display = "block";
  var htmlEl = document.getElementById('outMontly');
  htmlEl.innerText = payment;
}

$('#btnCalculate').click(function(){

 var cost = document.getElementById('inCost').value;

 var downPayment = document.getElementById('inDown').value;
 var interst = document.getElementById('inAPR').value;
 var term   = document.getElementById('inPeriod').value;

 if (cost < 0 || downPayment < 0 || interst < 0 || term <0) {
   return false;
 }
 if (cost == "" || downPayment == "" || interst == "" || term == "") {
   return false;
 }

 var amount = cost - downPayment;
 var pmt = calculateMortgage(amount,interst,term);
 postPayments(pmt);

});


/* Disable Select Mobile & Tablets

'use strict';
$(document).ready(function() {
  checkSize();
  $(window).resize(checkSize);
});

function checkSize(){
  if (window.matchMedia("(min-width: 768px)").matches) {
    $("select").niceSelect();
  } else {
    $("select").niceSelect("destroy");
  }
}  */