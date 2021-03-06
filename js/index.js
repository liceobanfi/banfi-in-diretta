

if( document.readyState !== 'loading' ) {
    main();
} else {
    document.addEventListener('DOMContentLoaded', function () {
        main();
    });
}

//title font lazy loading
// var font = new FontFace("Awesome Font", "url(https://fonts.googleapis.com/css?family=Libre+Franklin:600)", {
//   style: 'normal', unicodeRange: 'U+000-5FF', weight: '400'
// });

// // don't wait for the render tree, initiate an immediate fetch!
// font.load().then(function() {
//   // apply the font (which may re-render text and cause a page reflow)
//   // after the font has finished downloading
//   document.fonts.add(font);
//   document.body.style.fontFamily = "Awesome Font, serif";

//   // OR... by default the content is hidden, 
//   // and it's rendered after the font is available
//   var content = document.getElementById("content");
//   content.style.visibility = "visible";

//   // OR... apply your own render strategy here... 
// });


function main(){

  const gid = id => document.getElementById(id)
  const $ = document.querySelector.bind(document)
  const $$ = document.querySelectorAll.bind(document)

  // const ael = (s, type, callBack) => $(s).addEventListener(type, callBack)
  // const bt = (s, callBack) => ael(s, 'click', callBack)
  // const sleep = time => new Promise( resolve => setTimeout(resolve, time))
  
  let errorBoxTimer = null;
  function errorMessageBox(error){
    if(error) $('.error_modal p').innerText = error
    $('.error_modal').classList.add('visible');
    if(!errorBoxTimer) errorBoxTimer = setTimeout(e =>{
      $('.error_modal').classList.remove('visible')
      errorBoxTimer = null
    },6000)
  }

  const selection = {
    course: null,
    date: null
  }

  //toggle forms
  $$(".toggle-box").forEach( e =>{
    e.addEventListener('click', e =>
     $(".form_outer_line").classList.toggle("mail_box_selected")
    )
  });

  //hande modal error
  let url = new URL(window.location.href);
  let message = url.searchParams.get("message");
  if(message){
    if(message == 'success'){
      $('.success_modal').classList.add('visible');
    }else{
      $('.error_modal').classList.add('visible');
      setTimeout(e => history.pushState({}, "", "index.php"), 6000);
    }
    //remove the get params from the url
  }

  //close error modal
  $('.error_modal .close').addEventListener('click', e=>
      e.target.parentNode.classList.remove('visible')
    );
  //close success modal
  $('.success_modal .close_btn').addEventListener('click', e=>{
      $('.success_modal').classList.remove('visible')
      history.pushState({}, "", "index.php")
    });




  //set the default selected course as the first element in the list
  const firstCourseElem = $("ul.course_grid>li");
  if(firstCourseElem){
    selection.course = firstCourseElem
  }else{
    console.log("no courses")
  }
  updateSelectionStatus()

  //toggle courses
  $("ul.course_grid").addEventListener('click', e =>{
    //the clicked li element in the grid
    let clickedLI = e.target.tagName == "LI" ? e.target : e.target.parentNode
    if(clickedLI.tagName !== "LI") clickedLI = false;

    if(clickedLI && selection.course !== clickedLI){
      selection.course.classList.remove('selected')
      clickedLI.classList.add('selected')
      selection.course = clickedLI;

      index = Array.from(clickedLI.parentNode.children).indexOf(clickedLI)
      //toggle corresponding course calendar
      $("ul.dates_grid:not(.hidden)").classList.add("hidden")
      $$("ul.dates_grid")[index].classList.remove("hidden")
      if(selection.date){
        selection.date.classList.remove("selected")
        selection.date = null
      }
      // console.log(e, selection, index)
      updateSelectionStatus()
    }
  });

  //select course date
  $$("ul.dates_grid").forEach(e =>{
    e.addEventListener('click', e=>{
      //the clicked li element in the grid
      let clickedLI = e.target.tagName == "LI" ? e.target : e.target.parentNode
      if(clickedLI.tagName !== "LI") clickedLI = false;

      const badClasses = clickedLI.className == "disabled" || clickedLI.className == "month_label"

      if(clickedLI && selection.date !== clickedLI && !badClasses ){
        if(selection.date){
          selection.date.classList.remove("selected")
        }
        clickedLI.classList.add("selected")
        selection.date = clickedLI

        updateSelectionStatus()
      }
    })
  });

  // function getmonth(){
  //     let month = undefined

  //     const siblings = selection.date.parentNode.children
  //     const index = Array.from(siblings).indexOf(selection.date)
  //     for(let i= index-1; i>=0; i--){
  //       if(siblings[i].className == "month_label"){
  //         month = siblings[i].firstChild.innerText
  //         break;
  //       }
  //     }
  //     return month;
  // }
  function updateSelectionStatus(){
    let out = "nessun corso selezionato"
    if(selection.course && selection.date){
      const course = selection.course.firstChild.innerText
      const number = selection.date.firstChild.innerText
      const month = selection.date.dataset.month
      if(course && number && month){
        out = `${course} - ${number} ${month}`
      }
    }
    gid("selection_status").innerText = out
  }


  function post(path, params, method='post') {
    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    const form = document.createElement('form');
    form.method = method;
    form.action = path;

    for (const key in params) {
      if (params.hasOwnProperty(key)) {
        const hiddenField = document.createElement('input');
        hiddenField.type = 'hidden';
        hiddenField.name = key;
        hiddenField.value = params[key];

        form.appendChild(hiddenField);
      }
    }
    document.body.appendChild(form);
    form.submit();
  }

  function isMail(mail){
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(mail).toLowerCase());
  }

  //send form button
  gid("js_prenota_btn").addEventListener('click', e =>{
    //molto bello
    let inputNames = [
      "email",
      "cognome",
      "nome",
      "comune",
      "scuola"
    ];
    const formValid = inputNames.every(e => $(".form_box input[name="+e+"]").value.length > 0)
    const mailValid = isMail($(".form_box input[name=email]").value)

    if(!formValid){
      errorMessageBox("completare tutti i campi")
    }else if(!mailValid){
      errorMessageBox("indirizzo email non valido")
    }else if(!selection.course || !selection.date){
      errorMessageBox("selezionare una data");
    }else{
      const course = selection.course.firstChild.innerText
      const number = selection.date.firstChild.innerText
      const month = selection.date.dataset.month
      let data = {
        course: course,
        number: number,
        month: month
      }

      inputNames.forEach(e =>{
       let val =  $(".form_box input[name="+e+"]").value
       data[e] = val
      });
      post('index_formhandler.php', data)
    }

  });
  

  //send mail button
  gid("js_conferm_btn").addEventListener('click', e =>{
    const mailValid = isMail($(".mail_box input[name=email]").value)
    if(!mailValid){
      errorMessageBox("inserisci una mail valida")
    }else{
      let email = $(".mail_box input[name=email]").value
      post('gestionemail.php', {email})
    }

  });


};
