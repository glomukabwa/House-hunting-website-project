const modal = document.getElementById("myModal");
const modalImg = document.getElementById("img01");
const close = document.getElementsByClassName("close")[0];
const next = document.getElementsByClassName("next")[0];
const prev = document.getElementsByClassName("prev")[0];
const slideNumber = document.getElementById("slideNumber");

let groupImages = [];
let currentIndex = 0;

document.querySelectorAll(".myImg").forEach((img) => {
    img.onclick = function() {
        const parentColumn = img.closest(".img-column");

        groupImages = parentColumn.querySelectorAll(".myImg");

        groupImages.forEach((gImg, index) => {
            if (gImg === img) {
                currentIndex = index;
            }
        });

        modal.style.display = "block";
        modalImg.src = img.src;
    };
});
next.onclick = function() {
    showSlide(currentIndex + 1);
};
prev.onclick = function() {
    showSlide(currentIndex - 1);
};
function showSlide(index) {
    if (index >= groupImages.length) index = 0;
    if (index < 0) index = groupImages.length - 1;
    modalImg.src = groupImages[index].src;
    currentIndex = index;
}
close.onclick = function() {
    modal.style.display = "none";
};
modal.onclick = function(event) {
    if (event.target === modal) modal.style.display = "none";
};

document.querySelectorAll(".myImg").forEach((img) => {
  img.onclick = function() {
    const parentColumn = img.closest(".img-column");
    groupImages = parentColumn.querySelectorAll(".myImg");

    groupImages.forEach((gImg, index) => {
      if (gImg === img) {
        currentIndex = index;
      }
    });

    modal.style.display = "block";
    modalImg.src = img.src;
    slideNumber.textContent = `${currentIndex + 1} / ${groupImages.length}`;
  };
});

function showSlide(index) {
  if (index >= groupImages.length) index = 0;
  if (index < 0) index = groupImages.length - 1;

  modalImg.src = groupImages[index].src;
  currentIndex = index;
  slideNumber.textContent = `${currentIndex + 1} / ${groupImages.length}`;
}
