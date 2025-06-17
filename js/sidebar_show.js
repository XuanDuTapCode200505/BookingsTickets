$(document).ready(function () {
  const $slideshow = $(".service-menu-showslide ul");
  const $slides = $(".service-menu-showslide ul li");

  if (!$slideshow.length || !$slides.length) return;

  let currentIndex = 0;
  const totalSlides = $slides.length;

  // Thiết lập CSS ban đầu
  $slideshow.css({
    display: "flex",
    transition: "transform 0.5s ease",
  });

  $slides.css({
    flex: "0 0 100%",
    width: "100%",
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
    $slideshow.css("transform", `translateX(-${currentIndex * 100}%)`);
  }

  // Tự động chuyển slide
  function startSlideshow() {
    return setInterval(() => {
      showSlide(currentIndex + 1);
    }, 3000);
  }

  let slideInterval = startSlideshow();

  // Dừng slideshow khi hover
  $slideshow.hover(
    function () {
      clearInterval(slideInterval);
    },
    function () {
      slideInterval = startSlideshow();
    }
  );
});
