

if( document.readyState !== 'loading' ) {
    main();
} else {
    document.addEventListener('DOMContentLoaded', function () {
        main();
    });
}


function main(){

  const gid = id => document.getElementById(id)
  const $ = document.querySelector.bind(document)
  const $$ = document.querySelectorAll.bind(document)

  // const ael = (s, type, callBack) => $(s).addEventListener(type, callBack)
  // const bt = (s, callBack) => ael(s, 'click', callBack)
  // const sleep = time => new Promise( resolve => setTimeout(resolve, time))

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
      const name = selection.course.firstChild.innerText
      const number = selection.date.firstChild.innerText
      const month = selection.date.dataset.month
      if(name && number && month){
        out = `${name} - ${number} ${month}`
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

    if(!formValid){
      alert("completare tutti i campi")
    }else if(!selection.course || !selection.date){
      alert("selezionare una data");
    }else{
      const name = selection.course.firstChild.innerText
      const number = selection.date.firstChild.innerText
      const month = selection.date.dataset.month
      let data = {
        name: name,
        number: number,
        month: month
      }

      inputNames.forEach(e =>{
       let val =  $(".form_box input[name="+e+"]").value
       data[e] = val
      });

      post('form.php', data)
    }


  });

};
