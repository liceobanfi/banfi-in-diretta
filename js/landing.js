

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


