$(function() {
var acc = document.getElementsByClassName("accordion");
var panel = document.getElementsByClassName("panel");
var icons = document.getElementsByClassName("i");
var i;

for (var i = 0; i < acc.length; i++) {
    acc[i].onclick = function() {
	var setClasses = !this.classList.contains('active');
	setClass(acc, 'active', 'remove');
	setClass(panel, 'show', 'remove');
	if (setClasses) {
	    this.classList.toggle("active");
	    this.nextElementSibling.classList.toggle("show");
	}
    }
}

function setClass(els, className, fnName) {
    for (var i = 0; i < els.length; i++) {
	els[i].classList[fnName](className);
    }
}

$('button').click(function() {
	$('i').not($(this).find('i')).removeClass('fa-angle-up')
	    $('i').not($(this).find('i')).addClass('fa-angle-down')
	    $(this).find('i').toggleClass('fa-angle-down fa-angle-up')
	    });
    }
   )
