$(document).ready(function () {
  function showTheaters(city) {
    $(".cgv-theaters").removeClass("active");
    $(".cgv-city-col li").removeClass("active");
    $("#theaters-" + city).addClass("active");
    $("#city-" + city).addClass("active");
  }
  // Gán sự kiện click cho các tỉnh/thành
  $(".cgv-city-col li").click(function () {
    var id = $(this).attr("id");
    if (id && id.startsWith("city-")) {
      var city = id.replace("city-", "");
      showTheaters(city);
    }
  });
  // Mặc định hiện Hồ Chí Minh
  showTheaters("hcm");
});
