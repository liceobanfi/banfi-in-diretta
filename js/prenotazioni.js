

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

  function deleteRow(e){
    let parent = e.target.parentNode
    let id = parent.querySelector('.btn.invisible').dataset.id
    parent.innerHTML = "<a>attendere..</a>"
    post("", {deleteid: id})
    // alert(id)
  }

  function restoreRow(e){
    e.target.parentNode.querySelector('.btn.invisible').classList.remove('invisible')
    e.target.parentNode.querySelector('.btn.confirm').remove()
    e.target.remove()
  }

  $$("table .btn").forEach( e =>{
    e.addEventListener('click', e =>{

     e.target.classList.toggle('invisible')

     let confirmBt = document.createElement('a')
     confirmBt.innerText = 'conferma'
     confirmBt.classList.add('btn', 'confirm')
     confirmBt.addEventListener('click', deleteRow)

     let cancelBt = document.createElement('a')
     cancelBt.classList.add('btn', 'cancel')
     cancelBt.innerText = 'annulla'
     cancelBt.addEventListener('click', restoreRow)

     e.target.parentNode.appendChild(confirmBt)
     e.target.parentNode.appendChild(cancelBt)
    })
  });

}
