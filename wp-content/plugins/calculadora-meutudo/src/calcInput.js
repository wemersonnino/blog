export default function CalcInput(props) {

    return(
        <>
            <main id={"input-calc"} className={"input-calc bg-silver"}>
                <div className={"row"}>
                    <div id={"boxBeneficioSalario"} className={"col"}>
                        <label htmlFor="beneficioSalario" className={"form-label"}>Valor do benefício / salário
                            <span
                            className={"align-content-around text-start"}>
                                <a id={"beneficioSalario-infor"} className={"float-end"}
                                                                           href="#">i
                                </a>
                            </span>
                        </label>
                        <div className={"input-group mb-3 form-floating"}>
                            <span className={"input-group-text"} id={"basic-addon1"}>R$</span>
                            <input
                                inputMode={"numeric"}
                                type={"text"}
                                min={1} max={50000000}
                                id={"beneficioSalario"}
                                className={"form-control"}
                                name={"beneficioSalario"}
                                placeholder={"0,00"}
                                aria-label={"Valor do benefício / salário"}
                            />
                        </div>
                    </div>
                    <div id={"boxParcelas"} className="col">
                        <label htmlFor="parcelas" className={"form-label text-start"}>
                            Parcela de empréstimos do benefício
                            <span>
                                <a id={"parcelas-infor"} className={"float-end"} href="#">i</a>
                            </span>
                        </label>
                        <div className={"input-group mb-3 form-floating"}>
                            <span className={"input-group-text"} id={"basic-addon2"}>R$</span>
                            <input
                                inputMode={"numeric"}
                                type={"text"}
                                min={1} max={500000000}
                                id={"parcelas"}
                                className={"form-control"}
                                placeholder={"0,00"}
                                aria-label={"Parcela de empréstimos do benefício"}
                            />
                        </div>
                    </div>
                </div>
            </main>
        </>
    )
}