import React from "react";
import './calcInput'
import CalcInput from "./calcInput";
import CalcFooter from "./calcFooter";
export default function CalcFront(prop) {
    prop.titleCalc = "Cálculo de Margem Consignável";

    return(
        <>
            <section id={"box-calculadora"} className={"calculadora"}>
                <header id={"titulo-calculadora"}>
                    <h4>{prop.titleCalc}</h4>
                </header>
                <CalcInput></CalcInput>
                <CalcFooter></CalcFooter>
            </section>
        </>
    );
}