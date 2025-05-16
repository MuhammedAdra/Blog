const openBtn = document.getElementById("open__nav-btn");
const closeBtn = document.getElementById("close__nav-btn");
const navItems = document.querySelector(".nav__items");

openBtn.addEventListener("click", () => {
  navItems.style.display = "flex";
  openBtn.style.display = "none";
  closeBtn.style.display = "inline-block";
});

closeBtn.addEventListener("click", () => {
  navItems.style.display = "none";
  openBtn.style.display = "inline-block";
  closeBtn.style.display = "none";
});
