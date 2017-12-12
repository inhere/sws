常见的微服务组件及概念

- 服务注册，服务提供方将自己调用地址注册到服务注册中心，让服务调用方能够方便地找到自己。
- 服务发现，服务调用方从服务注册中心找到自己需要调用的服务的地址。
- 负载均衡，服务提供方一般以多实例的形式提供服务，负载均衡功能能够让服务调用方连接到合适的服务节点。并且，节点选择的工作对服务调用方来说是透明的。
- 服务网关，服务网关是服务调用的唯一入口，可以在这个组件是实现用户鉴权、动态路由、灰度发布、A/B测试、负载限流等功能。
- 配置中心，将本地化的配置信息（properties, xml, yaml等）注册到配置中心，实现程序包在开发、测试、生产环境的无差别性，方便程序包的迁移。
- API管理，以方便的形式编写及更新API文档，并以方便的形式供调用者查看和测试。
- 集成框架，微服务组件都以职责单一的程序包对外提供服务，集成框架以配置的形式将所有微服务组件（特别是管理端组件）集成到统一的界面框架下，让用户能够在统一的界面中使用系统。
- 分布式事务，对于重要的业务，需要通过分布式事务技术（TCC、高可用消息服务、最大努力通知）保证数据的一致性。
- 调用链，记录完成一个业务逻辑时调用到的微服务，并将这种串行或并行的调用关系展示出来。在系统出错时，可以方便地找到出错点。
- 支撑平台，系统微服务化后，系统变得更加碎片化，系统的部署、运维、监控等都比单体架构更加复杂，那么，就需要将大部分的工作自动化。现在，可以通过Docker等工具来中和这些微服务架构带来的弊端。 例如持续集成、蓝绿发布、健康检查、性能健康等等。严重点，以我们两年的实践经验，可以这么说，如果没有合适的支撑平台或工具，就不要使用微服务架构。

一般情况下，如果希望快速地体会微服务架构带来的好处，使用Spring Cloud提供的服务注册（Eureka）、服务发现（Ribbon）、服务网关（Zuul）三个组件即可以快速入门。其它组件则需要根据自身的业务选择性使用。

容错管理工具，旨在通过控制服务和第三方库的节点,从而对延迟和故障提供更强大的容错能力。

oo

- Config 配置管理开发工具包，可以让你把配置放到远程服务器，目前支持本地存储、Git以及Subversion。
- Edge Tools 边缘服务工具，是提供动态路由，监控，弹性，安全等的边缘服务。
- API manager 
- Log  记录完成一个业务逻辑时调用到的微服务，并将这种串行或并行的调用关系展示出来。
- SRAD Service registration and discovery
    - consul
    - Zookeeper

## spring框架及spring cloud框架主要组件

### spring 顶级项目：

```text
   Spring IO platform:用于系统部署，是可集成的，构建现代化应用的版本平台，具体来说当你使用maven dependency引入spring jar包时它就在工作了。
   Spring Boot:旨在简化创建产品级的 Spring 应用和服务，简化了配置文件，使用嵌入式web服务器，含有诸多开箱即用微服务功能，可以和spring cloud联合部署。
   Spring Framework:即通常所说的spring 框架，是一个开源的Java/Java EE全功能栈应用程序框架，其它spring项目如spring boot也依赖于此框架。
   Spring Cloud：微服务工具包，为开发者提供了在分布式系统的配置管理、服务发现、断路器、智能路由、微代理、控制总线等开发工具包。
   Spring XD：是一种运行时环境（服务器软件，非开发框架），组合spring技术，如spring batch、spring boot、spring data，采集大数据并处理。
   Spring Data：是一个数据访问及操作的工具包，封装了很多种数据及数据库的访问相关技术，包括：jdbc、Redis、MongoDB、Neo4j等。
   Spring Batch：批处理框架，或说是批量任务执行管理器，功能包括任务调度、日志记录/跟踪等。
   Spring Security：是一个能够为基于Spring的企业应用系统提供声明式的安全访问控制解决方案的安全框架。
   Spring Integration：面向企业应用集成（EAI/ESB）的编程框架，支持的通信方式包括HTTP、FTP、TCP/UDP、JMS、RabbitMQ、Email等。
   Spring Social：一组工具包，一组连接社交服务API，如Twitter、Facebook、LinkedIn、GitHub等，有几十个。
   Spring AMQP：消息队列操作的工具包，主要是封装了RabbitMQ的操作。
   Spring HATEOAS：是一个用于支持实现超文本驱动的 REST Web 服务的开发库。
   Spring Mobile：是Spring MVC的扩展，用来简化手机上的Web应用开发。
   Spring for Android：是Spring框架的一个扩展，其主要目的在乎简化Android本地应用的开发，提供RestTemplate来访问Rest服务。
   Spring Web Flow：目标是成为管理Web应用页面流程的最佳方案，将页面跳转流程单独管理，并可配置。
   Spring LDAP：是一个用于操作LDAP的Java工具包，基于Spring的JdbcTemplate模式，简化LDAP访问。
   Spring Session：session管理的开发工具包，让你可以把session保存到redis等，进行集群化session管理。
   Spring Web Services：是基于Spring的Web服务框架，提供SOAP服务开发，允许通过多种方式创建Web服务。
   Spring Shell：提供交互式的Shell可让你使用简单的基于Spring的编程模型来开发命令，比如Spring Roo命令。
   Spring Roo：是一种Spring开发的辅助工具，使用命令行操作来生成自动化项目，操作非常类似于Rails。
   Spring Scala：为Scala语言编程提供的spring框架的封装（新的编程语言，Java平台的Scala于2003年底/2004年初发布）。
   Spring BlazeDS Integration：一个开发RIA工具包，可以集成Adobe Flex、BlazeDS、Spring以及Java技术创建RIA。
   Spring Loaded：用于实现java程序和web应用的热部署的开源工具。
   Spring REST Shell：可以调用Rest服务的命令行工具，敲命令行操作Rest服务。
```
   
### spring cloud
   
   目前来说spring主要集中于spring boot（用于开发微服务）和spring cloud相关框架的开发，spring cloud子项目包括：
   
```text
   Spring Cloud Config：配置管理开发工具包，可以让你把配置放到远程服务器，目前支持本地存储、Git以及Subversion。
   Spring Cloud Bus：事件、消息总线，用于在集群（例如，配置变化事件）中传播状态变化，可与Spring Cloud Config联合实现热部署。
   Spring Cloud Netflix：针对多种Netflix组件提供的开发工具包，其中包括Eureka、Hystrix、Zuul、Archaius等。
   Netflix Eureka：云端负载均衡，一个基于 REST 的服务，用于定位服务，以实现云端的负载均衡和中间层服务器的故障转移。
   Netflix Hystrix：容错管理工具，旨在通过控制服务和第三方库的节点,从而对延迟和故障提供更强大的容错能力。
   Netflix Zuul：边缘服务工具，是提供动态路由，监控，弹性，安全等的边缘服务。
   Netflix Archaius：配置管理API，包含一系列配置管理API，提供动态类型化属性、线程安全配置操作、轮询框架、回调机制等功能。
   Spring Cloud for Cloud Foundry：通过Oauth2协议绑定服务到CloudFoundry，CloudFoundry是VMware推出的开源PaaS云平台。
   Spring Cloud Sleuth：日志收集工具包，封装了Dapper,Zipkin和HTrace操作。
   Spring Cloud Data Flow：大数据操作工具，通过命令行方式操作数据流。
   Spring Cloud Security：安全工具包，为你的应用程序添加安全控制，主要是指OAuth2。
   Spring Cloud Consul：封装了Consul操作，consul是一个服务发现与配置工具，与Docker容器可以无缝集成。
   Spring Cloud Zookeeper：操作Zookeeper的工具包，用于使用zookeeper方式的服务注册和发现。
   Spring Cloud Stream：数据流操作开发包，封装了与Redis,Rabbit、Kafka等发送接收消息。
   Spring Cloud CLI：基于 Spring Boot CLI，可以让你以命令行方式快速建立云组件。
```


## Dubbo

```text
dubbo-admin	
dubbo-cluster	
dubbo-common	
dubbo-config	
dubbo-container
dubbo-demo	
dubbo-filter	
dubbo-maven	Add 
dubbo-monitor	
dubbo-registry	
dubbo-remoting
dubbo-rpc	
dubbo-simple	
dubbo-test
```