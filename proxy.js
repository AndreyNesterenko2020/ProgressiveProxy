var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var fs = require("fs");

var completed = {};

function loop() {
  try {
    var data = fs.readFileSync("./JOBS_IN", 'utf8');
    var job_count = data.split(",").length-1;
    for(var i = 0; i < job_count; i++) {
      var id = data.split(",")[i].split(":")[0];
      if(id in completed) continue;
      completed[id] = true;
      var URL = atob(data.split(",")[i].split(":")[1]);
      console.log("new job for URL: "+URL);
      var request = new XMLHttpRequest();
      request.open("POST", URL.replace("\QUESTION_MARK/","?"));
      request.send();
      request.onreadystatechange = function(){
        if (this.readyState === 4) {
          try {
            fs.writeFileSync("./JOBS_OUT", "\n"+id+":"+btoa(this.responseText))+",";
            console.log("finished job for URL: "+URL);
          } catch (e) {}
        }
      }
      setTimeout(function(){
        if(request.readyState === 4) return;
        try {
          fs.writeFileSync("./JOBS_OUT", "\n"+id+":"+btoa("An error has occured.")+",");
          console.log("failed job for URL: "+URL);
        } catch (e) {}
      }, 15000);
    }
  } catch(error) {
    console.log(error);
  }
  setTimeout(loop, 1000);
}
loop();