import axios from 'axios'
import React, { useEffect, useState } from 'react'


function SignupForm() {
    const [formName, setFormName] = useState("")
    const [formEmail, setFormEmail] = useState("")
    const [formPassword, setFormPassword] = useState("")

    const [inputEmpty, setInputEmpty] = useState(false)

    const valuesFormName = (event:React.ChangeEvent<HTMLInputElement>)=>{
        setFormName(event.target.value);
    }
    const valuesFormEmail = (event:React.ChangeEvent<HTMLInputElement>)=>{
        setFormEmail(event.target.value);
    }
    const valuesFormPassword = (event:React.ChangeEvent<HTMLInputElement>)=>{
        setFormPassword(event.target.value);
    }

    const handleSubmit = () => {
        var dades = {
            Username : formName, 
            Email: formEmail, 
            Password_hash : formPassword, 
            }
            
        if(formName !== "" && formEmail !== "" && formPassword !== ""){
            axios.post("http://localhost:80/pr1/php/api/usuaris/signup",dades)
            .then((r) => {   
                console.log(r)
                
            })
            .catch(error => console.log(error))
            console.log(formName, formEmail, formPassword)
        }
      
    }

    const blur = () =>{
        if(formName.length < 3){
            setInputEmpty(true)
        }
        else{
            setInputEmpty(false)
        }

    }

        function testPassword(){
        //La contrasenya a de tenir entre 8 i 16 caracters, minim 1 numero, minim 1 minuscula i minim 1 majuscula. No pot contenir altres signes!
        var regexContrasenya = /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/;
        if(formPassword.length > 0)
            return regexContrasenya.test(formPassword)
        else return true
    }

    return (
        <div className='signup-form'>
            
            <br />
            <div className='container-signup'>
            <h1>Signup</h1>
                <form>
                    <div className="form-floating my-3 col-12">
                        <input onBlur={blur} value={formName} onChange={valuesFormName} type="text" className="form-control" id="floatingInputName" placeholder="Your name, here!"/>
                        <label htmlFor="floatingInputName">Your name</label>
                    </div>
                    {
                        inputEmpty == true ? <p>EL NOM A DE TENIR MINIM 3 CARACTERS</p> : <p></p>
                    }
                    <div className="form-floating mb-3 col-12">
                        <input value={formEmail} onChange={valuesFormEmail} type="email" className="form-control" id="floatingInputEmail" placeholder="name@example.com"/>
                        <label htmlFor="floatingInputEmail">Email address</label>
                    </div>
                    <div className="form-floating col-12">
                        <input onBlur={testPassword} value={formPassword} onChange={valuesFormPassword} type="password" className="form-control" id="floatingPassword" placeholder="Password"/>
                        <label htmlFor="floatingPassword">Password</label>
                    </div>
                    {
                        testPassword() ? <p></p> : <p>8-16 chars, 1num, 1minuscula, 1majuscula</p>
                    }
                </form>
                    {
                        testPassword()
                        ?
                        <button className='btn btn-dark my-3' onClick={()=>handleSubmit()}>SignUp!</button>
                        :
                        <button className='btn btn-dark my-3' disabled>SignUp!</button>
                    }
            </div>

        </div>
    )
}

export default SignupForm