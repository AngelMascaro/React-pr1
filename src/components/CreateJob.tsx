// import axios from 'axios'
import '../App.css'
import { useForm } from "react-hook-form";

interface IFormInputs {
    Username: string;
    Email: string;
    Password_hash: string;
    Birthday: Date
  }

function CreateJob() {

    const {
        register,
        watch,
        handleSubmit,
        formState: { errors }
    } = useForm<IFormInputs>();

    const onSubmit = (data: IFormInputs) => {
       
    };
    return (
        <div className='create-job'>
            <div>
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


                </form>
            </div>
            

            
        </div>
    )
}

export default CreateJob