const beneficioSalario = document.querySelector('#beneficioSalario');
const resultBeneficioSalario = document.querySelector('#resultBeneficioSalario');
const reg = new RegExp('^[0-9]*$');
const currency = 'BRL';

beneficioSalario.addEventListener('input',(evt)=>{
  let event = evt.target;
  changerValueInput(event)
  //console.log(event);
});

const changerValueInput = (elem) =>{
  if (elem.value === ""){
    console.log(elem.value);
  }
  else if (elem.value !== ""){
    console.log(elem.value );
  }
}

changerValueInput(beneficioSalario);





