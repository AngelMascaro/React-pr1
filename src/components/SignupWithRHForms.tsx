import React, { useState } from 'react'
// import ReactDOM from "react-dom";
import { useForm } from "react-hook-form";
import '../App.css';
import axios from 'axios'
import { useNavigate } from 'react-router-dom';



interface IFormInputs {
  Username: string;
  Email: string;
  Password_hash: string;
  Birthday: Date
}

function SignupWithRHForms() {

    //preview img
    const [file, setFile] = useState(null);
    const [filePre, setFilePre] = useState(null);
    const handleChange = async (
        event: React.ChangeEvent<HTMLInputElement>      
    ): Promise<any> => {
        const fileLoaded = URL.createObjectURL(event.target.files[0]);
        const files = event.target.files;

        setFile(files);
        setFilePre(fileLoaded);
        // console.log('files: ', files);
        // console.log('file ', file);
    };

    //per redirigir
    const navigate = useNavigate();

    const {
        register,
        watch,
        handleSubmit,
        formState: { errors }
    } = useForm<IFormInputs>();

    const onSubmit = (data: IFormInputs) => {
        let formData = new FormData();
        formData.append('file', file[0]);
        console.log("formdata",formData, file[0],"file")
           
        axios.post("http://localhost:80/pr1/php/api/usuaris/signup",data)
        .then((r) => {   
            console.log(r)

            if(r.status === 200){

                axios
                .post(
                    "http://localhost/pr1/php/api/usuaris/upload_image/" + r.data[0].User_id, 
                    formData, {headers : {'content-type': 'multipart/form-data'}}
                )
                .then((r)=>{
                    console.log("fdata",formData);    
                })
                setTimeout(() => {
                    navigate('/Profile/'+r.data[0].User_id)
                }, 2000);
            }
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
                        value:16, 
                        message:"max16"
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
      
      <div className="form-floating my-3 col-12">
            <input
                type="date"
                id="Birthday"
                className="form-control"
                placeholder="Your bithday, here!"
                 {...register(
                    "Birthday"
                )} 
            />
            <label htmlFor="bDate">Birthday</label>
        </div>

        <div className="form-floating my-3 col-12">
            <input
                type="file"
                onChange={handleChange}
                className="form-control"
                id='file'
                name='file'
                // {...register(
                //     "file"
                // )} 
            />
            <label htmlFor="file">Avatar</label>
        </div>

        <div className="img-preview-signup">
            <img
                src={filePre}
                style={{
                    display: 'flex',
                    border: '2px solid white',
                    maxWidth: '300px',
                    maxHeight: '300px',
                }}  
            />
        </div>
        
        
        <button className="btn btn-dark my-3">SignUp!</button>
      </form>
    </div>
  );
}

export default SignupWithRHForms