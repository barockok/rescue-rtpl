set :user, 'deployer'
set(:password) do
   Capistrano::CLI.ui.ask "Give me a ssh password: "
end



def red(str)
  "\e[31m#{str}\e[0m"
end

# Figure out the name of the current local branch
def current_git_branch
  branch = `git symbolic-ref HEAD 2> /dev/null`.strip.gsub(/^refs\/heads\//, '')
  puts "On branch #{red branch}"
  branch
end
current_git_branch
# Set the deploy branch to the current branch
set :branch, "production"
set :domain, 'rumahtiket.com'
set :applicationdir, "/var/www/rumahtiket.com/public/platform/"
set :deploy_path , "#{applicationdir}#{branch}"
set :repository,  "git@gitlab.barockprojects.com:rt-platform"
set :use_sudo, false
role :app, "#{domain}"
role :web, "#{domain}"
role :db,  "#{domain}", :primary => true
default_run_options[:pty] = true
namespace :update do
  task :default do
    command = "cd #{applicationdir} && git checkout #{branch} && git pull origin #{branch}"
    my_run(command)
  end
end

namespace :deploy  do
  desc "Search Remote Application Server Libraries"
  task :default do
    sudo "rm -rf #{applicationdir}"
    command = " 
            rm -rf #{applicationdir} &&
            mkdir #{applicationdir} &&
            cd #{applicationdir} &&
            git clone #{repository} #{applicationdir} &&
            git pull --all &&
            git checkout #{branch}
          "
    my_run(command)
  end
end

def my_run ( command ) 
  out = ''
  run command  do |channel, stream, data|
     out << data
  end
 puts "============================== "+red('OUTPUT')+" ==================================\n\n"
 puts out
 puts "\n========================================================================\n\n"
end


