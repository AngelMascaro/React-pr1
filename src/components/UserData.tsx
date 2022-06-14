// import axios from 'axios'
import '../App.css'

interface Props{
    Username:any
    Email:any
    Src:any
    Birthday:any
}

function UserData(props:Props) {

    return (
        <div className='userData'>

            <div className='container-signup'>
            <h1>USER PROFILE</h1>

                <div className="form-floating my-3 col-12">
                    <input disabled value={props.Username} type="text" className="form-control" id="floatingInputName" placeholder="Your name, here!"/>
                    <label htmlFor="floatingInputName">Your name</label>
                </div>

                <div className="form-floating mb-3 col-12">
                    <input disabled value={props.Email} type="email" className="form-control" id="floatingInputEmail" placeholder="name@example.com"/>
                    <label htmlFor="floatingInputEmail">Email address</label>
                </div>
                
                <div className="form-floating mb-3 col-12">
                    <input disabled value={props.Birthday} type="text" className="form-control" id="floatingInputBdate" placeholder="name@example.com"/>
                    <label htmlFor="floatingInputBdate">Birthday</label>
                </div>

                <div className="form-floating mb-3">
                    <img id="avatar_image" width="300px" height="200px" src={props.Src} className="rounded mx-auto d-block" />
                </div>

            </div>
        </div>
    )
}

export default UserData