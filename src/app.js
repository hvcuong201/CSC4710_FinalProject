function outOfStockAlert() {
  alert("Sorry! The current bean is out of stock.");
}

function emptyCartToCheckOutHandler() {
  alert("Your cart is empty!");
}

/*---Navigator Bar Appear Based On Scroll Action---*/
var prevScrollpos = window.pageYOffset;
window.onscroll = function() {
var currentScrollPos = window.pageYOffset;
  if (prevScrollpos > currentScrollPos) {
    document.getElementById("nav-container").style.top = "0";
  } else {
    document.getElementById("nav-container").style.top = "-50px";
  }
  prevScrollpos = currentScrollPos;
}

/*---Collapsibles for Admin Dash Board---*/  
var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
  coll[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var content = this.nextElementSibling;
    if (content.style.display === "block") {
      content.style.display = "none";
    } else {
      content.style.display = "block";
    }
  });
}