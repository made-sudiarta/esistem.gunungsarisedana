<x-filament::page>
    <div class="flex justify-center items-center h-screen bg-lime-50">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-lg">
            <!-- Logo -->
            <div class="text-center mb-6">
                <img src="{{ asset('images/your-logo.png') }}" alt="Logo" class="mx-auto h-12">
            </div>

            <!-- Form Title -->
            <h2 class="text-2xl font-semibold text-center text-lime-600 mb-6">Welcome Back :)</h2>

            <!-- Login Form -->
            <form method="POST" action="{{ route('filament.auth.login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500"
                        value="{{ old('email') }}" placeholder="Enter your email" autofocus>
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required
                        class="mt-1 block w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-lime-500 focus:border-lime-500"
                        placeholder="Enter your password">
                </div>

                <!-- Remember Me -->
                <div class="mb-4 flex items-center">
                    <input id="remember_me" name="remember" type="checkbox"
                        class="h-4 w-4 text-lime-500 border-gray-300 rounded focus:ring-lime-500">
                    <label for="remember_me" class="ml-2 text-sm text-gray-700">Remember me</label>
                </div>

                <!-- Login Button -->
                <div class="mt-6">
                    <button type="submit"
                        class="w-full bg-lime-600 text-white p-3 rounded-lg hover:bg-lime-700 focus:outline-none focus:ring-4 focus:ring-lime-300 transition duration-300">Sign In</button>
                </div>

                <!-- Sign up Link -->
                <div class="mt-4 text-center">
                    <span class="text-sm text-gray-600">or <a href="{{ route('filament.auth.register') }}"
                            class="text-lime-600 hover:underline">sign up for an account</a></span>
                </div>
            </form>
        </div>
    </div>
</x-filament::page>