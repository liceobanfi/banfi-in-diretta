

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
    const clickedElement = e.target.tagName == "LI" ? e.target : e.target.parentNode
    if(selection.course !== clickedElement && clickedElement.tagName == "LI"){
      selection.course.classList.remove('selected')
      clickedElement.classList.add('selected')
      selection.course = clickedElement;

      index = Array.from(clickedElement.parentNode.children).indexOf(clickedElement)
      //toggle course dates
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
      const clickedElement = e.target.tagName == "LI" ? e.target : e.target.parentNode
      const badClasses = clickedElement.className == "disabled" || clickedElement.className == "month_label"
      if(selection.date !== clickedElement && !badClasses && clickedElement.tagName == "LI"){
        if(selection.date){
          selection.date.classList.remove("selected")
        }
        clickedElement.classList.add("selected")
        selection.date = clickedElement
        // console.log(selection)
        updateSelectionStatus()
      }
    })
  });

  function updateSelectionStatus(){
    let out = "nessun corso selezionato"
    if(selection.course && selection.date){
      const name = selection.course.firstChild.innerText
      const number = selection.date.firstChild.innerText
      let month = undefined

      const siblings = selection.date.parentNode.children
      index = Array.from(siblings).indexOf(selection.date)
      for(let i= index-1; i>=0; i--){
        if(siblings[i].className == "month_label"){
          month = siblings[i].firstChild.innerText
          break;
        }
      }
      if(name && number && month){
        out = `${name} - ${number} ${month}`
      }
    }
    gid("selection_status").innerText = out
  }

};

/*
let colors = [
  "#FBEC6B",
  "#F4BC6B",
  "#EB8B6B",
  "#E0536B",
  "#D6306D",
  "#A32C68",
  "#722A65",
  "#422662",
  "#12255D",
  "#275B75",
  "#3C918B",
  "#52C6A2",
  "#66F8B6"
]

let c1 = [
 "#D6306D",
 "#BD2E6B",
 "#A52D69",
 "#8C2B68",
 "#732966",
 "#5B2864",
 "#422662"
]

let c2 = [
 "#422662",
 "#3E2F65",
 "#393868",
 "#35416C",
 "#30496F",
 "#2C5272",
 "#275B75"
]

let c3 = [
 "#275B75",
 "#2E6D7D",
 "#357F84",
 "#3D918C",
 "#44A293",
 "#4BB49B",
 "#52C6A2"
]

let out = [...c1,...c2,...c3].reduce( (a,c,i) => `${a}\n
.dates_grid ul li:nth-child(${i}){
  background-color: ${c};
}`, "")



out = [...c1,...c2,...c3].reduce( (a,c,i) => `${a}\n
.dates_grid ul li:hover:not(.month_label):not(.disabled):nth-child(${i}){
  background-color: ${c};
}`, "")
console.log(out)

*/
