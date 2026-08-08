import { useEffect } from "react";
import { useNavigate } from "react-router";
import PageMeta from "../../components/common/PageMeta";
import AuthLayout from "./AuthPageLayout";
import SignInForm from "../../components/auth/SignInForm";
import AuthService from "../../services/AuthService";

export default function SignIn() {
    const navigate = useNavigate();

    useEffect(() => {
        if (AuthService.isAuthenticated()) {
            navigate("/");
        }
    }, [navigate]);

    return (
        <>
            <PageMeta
                title="React.js SignIn Dashboard | TailAdmin - Next.js Admin Dashboard Template"
                description="This is React.js SignIn Tables Dashboard page for TailAdmin - React.js Tailwind CSS Admin Dashboard Template"
            />
            <AuthLayout>
                <SignInForm />
            </AuthLayout>
        </>
    );
}