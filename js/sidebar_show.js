document.addEventListener("DOMContentLoaded", function () {
  const slideshow = document.querySelector(".service-menu-showslide ul");
  const slides = document.querySelectorAll(".service-menu-showslide ul li");

  if (!slideshow || slides.length === 0) return;

  let currentIndex = 0;
  const totalSlides = slides.length;

  // Thiết lập CSS ban đầu
  slideshow.style.display = "flex";
  slideshow.style.transition = "transform 0.5s ease";
  slides.forEach((slide) => {
    slide.style.flex = "0 0 100%";
    slide.style.width = "100%";
  });

  // Hàm chuyển slide
  function showSlide(index) {
    if (index < 0) {
      currentIndex = totalSlides - 1;
    } else if (index >= totalSlides) {
      currentIndex = 0;
    } else {
      currentIndex = index;
    }
    slideshow.style.transform = `translateX(-${currentIndex * 100}%)`;
  }

  // Tự động chuyển slide
  function startSlideshow() {
    return setInterval(() => {
      showSlide(currentIndex + 1);
    }, 3000); // Chuyển slide mỗi 3 giây
  }

  let slideInterval = startSlideshow();

  // Dừng slideshow khi hover
  slideshow.addEventListener("mouseenter", () => {
    clearInterval(slideInterval);
  });

  // Tiếp tục slideshow khi mouse leave
  slideshow.addEventListener("mouseleave", () => {
    slideInterval = startSlideshow();
  });
});
