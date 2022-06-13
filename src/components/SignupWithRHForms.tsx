import React from "react";
import ReactDOM from "react-dom";
import { useForm } from "react-hook-form";
import '../App.css';
import axios from 'axios'
import { useNavigate } from 'react-router-dom';



interface IFormInputs {
  Username: string;
  Email: string;
  Password_hash: string;
}

function SignupWithRHForms() {
    const navigate = useNavigate();

  const {
    register,
    handleSubmit,
    formState: { errors }
  } = useForm<IFormInputs>();

  const onSubmit = (data: IFormInputs) => {

    axios.post("http://localhost:80/pr1/php/api/usuaris/signup",data)
    .then((r) => {   
        console.log(r)
        navigate('/Profile/'+r.data[0].User_id)
    })
    .catch(error => console.log(error))
    console.log(data)
  };

  return (
    <div className="container-signup">
    
    <h1>Signup React Hook Forms</h1>

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
                        value:8, 
                        message:"max8"
                        },  
                    }
                )} 
            />
            <label htmlFor="fName">First Name</label>
            <span className="text-danger d-block mb-2">
                {errors?.Username?.message}
            </span>
        </div>

      <div className="form-floating my-3 col-12">
            <input
                type="email"
                id="fEmail"
                className="form-control"
                placeholder="Your email, here!"
                {...register(
                    "Email",{
                    required:"camp requerit", 
                    }
                )} 
            />
            <label htmlFor="fEmail">Email</label>
            <span className="text-danger d-block mb-2">
                {errors?.Email?.message}
            </span>
        </div>

      <div className="form-floating my-3 col-12">
            <input
                type="password"
                id="fPass"
                className="form-control"
                placeholder="Your password, here!"
                {...register(
                    "Password_hash",{
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
                {errors?.Password_hash?.message}
            </span>
        </div>
         
        <button className="btn btn-dark my-3">SignUp!</button>
      </form>
    </div>
  );
}

export default SignupWithRHForms