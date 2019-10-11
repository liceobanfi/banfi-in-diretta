
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

  const ael = (s, type, callBack) => $(s).addEventListener(type, callBack)

  const bt = (s, callBack) => ael(s, 'click', callBack)
  
  const sleep = time => new Promise( resolve => setTimeout(resolve, time))



  // bt("info-item .btn", function(){
  //   // $(".container").toggleClass("log-in");
  //   ca(".container", "log-in")
  // });

  $$(".info-item .btn").forEach( e =>{
    e.addEventListener('click', e =>
                       $(".container").classList.toggle("auth")
                      )
  });


};
