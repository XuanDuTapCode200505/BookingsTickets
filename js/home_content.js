const slider = document.querySelector(".slider");
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");

const itemWidth = 260; // 300px ảnh + 20px margin
const itemsPerPage = 3;
const totalItems = slider.children.length;
let currentIndex = 0;

nextBtn.addEventListener("click", () => {
  if (currentIndex < totalItems - itemsPerPage) {
    currentIndex++;
    slider.style.transform = `translateX(-${itemWidth * currentIndex}px)`;
  }
});

prevBtn.addEventListener("click", () => {
  if (currentIndex > 0) {
    currentIndex--;
    slider.style.transform = `translateX(-${itemWidth * currentIndex}px)`;
  }
});
