var mapOpen = document.querySelector(".open-map");
var mapPopup = document.querySelector(".modal-content-map");
var mapClose = mapPopup.querySelector(".modal-content-close");

mapOpen.addEventListener("click", function(event) {
	event.preventDefault();
	console.log("click!");
	mapPopup.classList.add("modal-content-show");
	console.log("click!");
});

mapClose.addEventListener("click", function(event) {
	event.preventDefault();
	mapPopup.classList.remove("modal-content-show");
});

window.addEventListener("keydown", function(event) {
    if (event.keyCode == 27) {
        if (mapPopup.classList.contains("modal-content-show")) {
            mapPopup.classList.remove("modal-content-show");
        }
    }
});