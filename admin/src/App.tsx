
import { Routes, Route } from "react-router-dom";
import { RequireAuth } from "./components/auth/RequireAuth";
import SignIn from "./pages/AuthPages/SignIn";
import SignUp from "./pages/AuthPages/SignUp";
import NotFound from "./pages/OtherPage/NotFound";
import UserProfiles from "./pages/UserProfiles";
import Videos from "./pages/UiElements/Videos";
import Images from "./pages/UiElements/Images";
import Alerts from "./pages/UiElements/Alerts";
import Badges from "./pages/UiElements/Badges";
import Avatars from "./pages/UiElements/Avatars";
import Buttons from "./pages/UiElements/Buttons";
import LineChart from "./pages/Charts/LineChart";
import BarChart from "./pages/Charts/BarChart";
import Calendar from "./pages/Calendar";
import BasicTables from "./pages/Tables/BasicTables";
import FormElements from "./pages/Forms/FormElements";
import Blank from "./pages/Blank";
import AppLayout from "./layout/AppLayout";
import { ScrollToTop } from "./components/common/ScrollToTop";
import Home from "./pages/Dashboard/Home";

// Bookings
import BookingsAll from "./pages/Bookings/BookingsAll.tsx";
import Planning from "./pages/Bookings/Planning";
import History from "./pages/Bookings/History";
import Payments from "./pages/Bookings/Payments";

// Cars
import CarsAll from "./pages/Cars/CarsAll.tsx";
import Classes from "./pages/Cars/Classes";
import Features from "./pages/Cars/Features.tsx";

// Other
import ExtraServices from "./pages/ExtraServices/ExtraServices";
import Locations from "./pages/Locations/Locations";
import Reviews from "./pages/Reviews/Reviews";

// Users
import UsersAll from "./pages/Users/UsersAll.tsx";
import Roles from "./pages/Users/Roles";

export default function App() {
  return (
      <>
        <ScrollToTop />

        <Routes>
          {/* Публичные */}
          <Route path="/signin" element={<SignIn />} />
          <Route path="/signup" element={<SignUp />} />

          {/* Приватные */}
          <Route element={<RequireAuth />}>
            <Route element={<AppLayout />}>
              <Route index path="/" element={<Home />} />

              <Route path="/profile" element={<UserProfiles />} />
              <Route path="/calendar" element={<Calendar />} />
              <Route path="/blank" element={<Blank />} />

              {/* Bookings */}
              <Route path="/bookings/bookings-all" element={<BookingsAll />} />
              <Route path="/bookings/planning" element={<Planning />} />
              <Route path="/bookings/payments" element={<Payments />} />

              {/* Cars */}
              <Route path="/cars/cars-all" element={<CarsAll />} />
              <Route path="/car-classes" element={<Classes />} />
              <Route
                  path="/cars/configurations"
                  element={<Features />}
              />

              {/* Other */}
              <Route path="/extra-services" element={<ExtraServices />} />
              <Route path="/locations" element={<Locations />} />
              <Route path="/reviews" element={<Reviews />} />

              {/* Users */}
              <Route path="/users/users-all" element={<UsersAll />} />
              <Route path="/users/roles" element={<Roles />} />

              {/* Старые страницы */}
              <Route path="/form-elements" element={<FormElements />} />
              <Route path="/basic-tables" element={<BasicTables />} />

              <Route path="/alerts" element={<Alerts />} />
              <Route path="/avatars" element={<Avatars />} />
              <Route path="/badge" element={<Badges />} />
              <Route path="/buttons" element={<Buttons />} />
              <Route path="/images" element={<Images />} />
              <Route path="/videos" element={<Videos />} />

              <Route path="/line-chart" element={<LineChart />} />
              <Route path="/bar-chart" element={<BarChart />} />
            </Route>
          </Route>

          <Route path="*" element={<NotFound />} />
        </Routes>
      </>
  );
}
