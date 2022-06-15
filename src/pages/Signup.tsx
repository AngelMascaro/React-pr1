import React from 'react'
import SignupWithRHForms from '../components/SignupWithRHForms'
// import SignupForm from '../components/SignupForm'
import { useState } from 'react'
import {useLocation} from 'react-router-dom'; 

interface Props{
    logged:Function
}

function Signup(props:Props) {
    return (
        <div>
            
            <br />
            <SignupWithRHForms logged={props.logged}/>
            <br />
            {/* <SignupForm/> */}
        </div>
    )
}

export default Signup