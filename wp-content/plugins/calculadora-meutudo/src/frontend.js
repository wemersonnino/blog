import React, { useState } from "react"
import ReactDOM from "react-dom"
import CalcFront from "./calcFront";

const divsToUpdate = document.querySelectorAll(".boilerplate-update-me")

divsToUpdate.forEach(div => {
  const data = JSON.parse(div.querySelector("pre").innerText)
  ReactDOM.render(<OurComponent {...data} />, div)
  div.classList.remove("boilerplate-update-me")
})

function OurComponent(props) {
  const [showSkyColor, setShowSkyColor] = useState(true)
  const [showGrassColor, setShowGrassColor] = useState(true)

  return (
    <div className="bg-amber-200 border-2 border-amber-300 p-4 my-3 rounded shadow-md">
        <CalcFront/>
    </div>
  )
}
