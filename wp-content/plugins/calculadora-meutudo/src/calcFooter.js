export default function CalcFooter(props) {
props.titleFooterCalc = "Resultado da margem consignável";
props.titleMargemPermitida = "Margem permitida";
props.titleMargemDisponível = "Margem disponível";
    return(
        <>

            <footer id={"footer-input-calc"}>
                <header>
                    <h4>{props.titleFooterCalc}</h4>
                </header>
                <div className="w-100"></div><br/>
                <main>
                    <div className={"row justify-content-around"}>
                        <div className={"col-md-4 col-lg-4 col-xl-4 col-sm-12"}>
                            <h3>{props.titleMargemPermitida}</h3>
                            <p>
                                Com o valor de seu benefício / salário atual, esta é a margem permitida para empréstimo consignado.
                            </p>
                        </div>
                        <div className={"col-md-4 col-lg-4 col-xl-4 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0"}>
                            <div className={"row justify-content-start"}>
                                <div className={"col"}>
                                    <p className={"fs-6"}>R$
                                        <span id={"resultBeneficioSalario"}>000,00</span>
                                    </p>
                                </div>
                                <div className={"col"}>
                                    <p className={"text-end"}>
                                        <a className={"btn-link text-light"} href="#">i</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className={"row justify-content-around"}>
                        <div className={"col-md-4 col-lg-4 col-xl-4 col-sm-12"}>
                            <h3>{props.titleMargemDisponível}</h3>
                            <p>
                                Parte de sua margem permitida já está sendo utilizada com outro consignável, portanto a sua margem disponível é esta.
                            </p>
                        </div>
                        <div className={"col-md-4 col-lg-4 col-xl-4 col-sm-12 mb-4 mt-5 mt-md-0 mt-lg-0 mt-xl-0"}>
                            <div className={"row justify-content-start"}>
                                <div className={"col"}>
                                    <p className={"fs-6"}>R$ 000,00</p>
                                </div>
                                <div className={"col"}>
                                    <p className={"text-end"}>
                                        <a className={"btn-link text-light"} href="#">i</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </footer>

        </>
    )
}