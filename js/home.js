function updateLS() {
    LS.setItem("info", JSON.stringify(info));
  }
  
  document.getElementById("personas").addEventListener("click", function (e) {
    e.preventDefault();
  
    info.checkerInfo.mode = "personas";
    updateLS();
  
    window.location.href = "./user.html";
  });
  
  document.getElementById("mano").addEventListener("click", function (e) {
    e.preventDefault();
    
    info.checkerInfo.mode = "mano";
    updateLS();
  
    window.location.href = "./cedula.html";
  });
  