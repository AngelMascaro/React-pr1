// import React from 'react'

// function Login() {
//     return (
//         <div>
//         </div>
//     )
// }

// export default Login

import React, { useState } from 'react'
// import ReactDOM from "react-dom";
import { useForm } from "react-hook-form";
import '../App.css';
import axios from 'axios'
import { useNavigate } from 'react-router-dom';



interface IFormInputs {
  Username: string;
  Plain_password: string;
}

interface Props{
    logged:Function
}

function Login(props:Props) {

    //per redirigir
    const navigate = useNavigate();

    const {
        register,
        handleSubmit,
        formState: { errors }
    } = useForm<IFormInputs>();

    const onSubmit = (data: IFormInputs) => {

        setShowError(false)
        
        axios.post("http://localhost:80/pr1/php/api/usuaris/login",data)
        .then((r) => {   
            // console.log(r)
            if(r.status === 200){
                //modifiquem state de app
                props.logged(r.data.User_id, r.data.Username)
                //mostrem spinner
                spin()
                //redirigim a perfil
                setTimeout(() => {
                    navigate('/Profile/'+r.data.User_id)
                }, 2000);
            }
        })
        .catch(error => setShowError(true))
        // console.log(data)
    };

    //spinner i error de login
    const [showSpinner, setShowSpinner] = useState(false)
    const [showError, setShowError] = useState(false)
    const spin = ()=>{
        showSpinner == true ? setShowSpinner(false) : setShowSpinner(true)
    }
    const error = ()=>{
        return <div><p>Usuari o Contrassenya no Valid</p></div>
    }

  return (
    <div>
        <br />
    <div className="container-signup">
    
    <h1>Login</h1>

      <form onSubmit={handleSubmit(onSubmit)}>

      <div className="form-floating my-3 col-12">
            <input
                type="text"
                id="fName"
                className="form-control"
                placeholder="Your Username, here!"
                {...register(
                    "Username",{
                    required:"camp requerit", 
                    minLength:{
                        value:3, 
                        message:"min3"
                        }, 
                    maxLength:{
                        value:16, 
                        message:"max16"
                        },  
                    }
                )} 
            />
            <label htmlFor="fName">Username</label>
            <span className="text-danger d-block mb-2">
                {errors?.Username?.message}
            </span>
        </div>

      <div className="form-floating my-3 col-12">
            <input
                type="password"
                id="fPass"
                className="form-control"
                placeholder="Your password, here!"
                {...register(
                    "Plain_password",{
                    required:"camp requerit", 
                    minLength:{
                        value:8, 
                        message:"1min, 1maj, 8-16chars, 1number"
                        }, 
                    maxLength:{
                        value:16, 
                        message:"1min, 1maj, 8-16chars, 1number"
                        },  
                    pattern:{
                        value: /^(?=\w*\d)(?=\w*[A-Z])(?=\w*[a-z])\S{8,16}$/, 
                        message:"1min, 1maj, 8-16chars, 1number"
                        } 
                    }
                )} 
            />
            <label htmlFor="fPass">Password</label>
            <span className="text-danger d-block mb-2">
                {errors?.Plain_password?.message}
            </span>
            <br />
        {
            showSpinner === true ?
            <div>
                <div className="spinner-grow text-primary" role="status"></div>
                <div className="spinner-grow text-secondary" role="status"></div>
                <div className="spinner-grow text-success" role="status"></div>
                <div className="spinner-grow text-danger" role="status"></div>
                <div className="spinner-grow text-warning" role="status"></div>
                <div className="spinner-grow text-info" role="status"></div>
            </div>
            :
            <p></p>
        }
        {
            showError === true ? 
            error()
            :
            <p></p>
        }
        </div>
      
        
        <button className="btn btn-dark my-3">Login!</button>
      </form>
    </div>
    </div>
  );
}

export default Login